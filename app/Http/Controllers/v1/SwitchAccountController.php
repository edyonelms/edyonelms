<?php

namespace App\Http\Controllers\v1;

use App\Models\Organization;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SwitchAccountController extends ApiController
{
    /**
     * POST /api/v1/switch-account/add
     *
     * Log in with credentials and return an enriched profile snapshot for the
     * app's local "added accounts" list. The app stores { token, snapshot }
     * locally; the user can switch between stored accounts without re-login.
     *
     * Body:
     *   - login_type: 'student' | 'teacher' (required)
     *   - admission_number: string (required when login_type=student)
     *   - email: string (required when login_type=teacher)
     *   - password: string (required)
     *
     * Returns: { user, token, token_type, snapshot }
     */
    public function add(Request $request)
    {
        $validationErr = $this->validateWith($request, [
            'login_type'       => 'required|in:student,teacher',
            'admission_number' => 'required_if:login_type,student|string',
            'email'            => 'required_if:login_type,teacher|email',
            'password'         => 'required|string',
        ]);
        if ($validationErr) return $validationErr;

        $user = null;

        if ($request->login_type === 'student') {
            $studentDetail = StudentDetail::where('admission_no', $request->admission_number)->first();
            if (!$studentDetail) {
                return $this->error('No account found with this admission number.', 401);
            }
            $user = $studentDetail->user()->where('role', 'user')->first();
            if (!$user) {
                return $this->error('No valid student account for this admission number.', 401);
            }
        } else {
            $user = User::where('email', $request->email)->where('role', 'teacher')->first();
            if (!$user) {
                return $this->error('No teacher account found with this email.', 401);
            }
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->error('The provided password is incorrect.', 401);
        }

        if (!$user->is_active) {
            return $this->error('This account has been deactivated.', 403);
        }

        $token    = $user->createToken('switch_account_' . now()->timestamp)->plainTextToken;
        $snapshot = $this->buildSnapshot($user);

        return $this->success(
            [
                'user'       => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token'      => explode('|', $token)[1] ?? $token,
                'token_type' => 'Bearer',
                'snapshot'   => $snapshot,
            ],
            'Account added successfully.'
        );
    }

    /**
     * GET /api/v1/switch-account/me
     *
     * Returns a compact "card" snapshot for the currently authenticated user.
     * The app calls this periodically to refresh locally-stored snapshots
     * (so the switcher shows the latest name / image / class).
     */
    public function me()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        return $this->success(
            $this->buildSnapshot($user),
            'Profile snapshot fetched successfully.'
        );
    }

    /**
     * POST /api/v1/switch-account/remove
     *
     * Revokes the **current** access token (the one in the Authorization header).
     * Use case: user removes an account from the device's switcher list.
     * The app sends this with the token-to-remove in Authorization, server
     * revokes only that token (other linked accounts keep working).
     */
    public function remove()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        return $this->success(
            ['user_id' => $user->id],
            'Account removed successfully. Token revoked.'
        );
    }

    /**
     * GET /api/v1/switch-account/schools
     *
     * Auto-discovery: returns all accounts sharing the current user's email + role
     * across organizations. Useful for users enrolled in multiple schools with the
     * same email.
     */
    public function schools()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $accounts = User::with('organization:id,name,logo,status')
            ->where('email', $user->email)
            ->where('role', $user->role)
            ->where('is_active', true)
            ->get()
            ->map(fn($u) => [
                'user_id'         => $u->id,
                'organization_id' => $u->organization_id,
                'is_current'      => $u->id === $user->id,
                'school'          => $u->organization ? [
                    'id'     => $u->organization->id,
                    'name'   => $u->organization->name,
                    'logo'   => $u->organization->logo,
                    'active' => (bool) $u->organization->status,
                ] : null,
            ])
            ->filter(fn($a) => $a['school'] !== null)
            ->values();

        return $this->success($accounts, 'Accounts fetched successfully.');
    }

    /**
     * POST /api/v1/switch-account/switch
     *
     * Revokes the current token, issues a new one for the same email+role in a
     * different organization. Used for the auto-discovery flow (above).
     *
     * Body: { "organization_id": 5 }
     */
    public function switch(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $validationErr = $this->validateWith($request, [
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);
        if ($validationErr) return $validationErr;

        $targetOrgId = (int) $request->organization_id;

        if ($user->organization_id === $targetOrgId) {
            return $this->error('You are already logged in to this school.', 400);
        }

        $targetUser = User::where('email', $user->email)
            ->where('role', $user->role)
            ->where('organization_id', $targetOrgId)
            ->where('is_active', true)
            ->first();

        if (!$targetUser) {
            return $this->error('No account found for your email in the selected school.', 404);
        }

        $user->currentAccessToken()->delete();
        $token = $targetUser->createToken('auth_token')->plainTextToken;

        return $this->response->authResponse(
            [
                'id'              => $targetUser->id,
                'name'            => $targetUser->name,
                'email'           => $targetUser->email,
                'role'            => $targetUser->role,
                'organization_id' => $targetUser->organization_id,
                'image'           => $targetUser->image,
            ],
            $token,
            'Switched to ' . (Organization::find($targetOrgId)?->name ?? 'school') . ' successfully.'
        );
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * Compact profile card for the switcher UI:
     * { user_id, name, email, role, image, organization{id,name,logo}, class_info{...}|null }
     */
    protected function buildSnapshot(User $user): array
    {
        $organization = $user->organization;

        $classInfo = null;
        $displayName = $user->name;
        $displayImage = $user->image;

        if ($user->role === 'user') {
            $student = StudentDetail::with(['standard:id,name,code', 'section:id,name'])
                ->where('user_id', $user->id)
                ->first();

            if ($student) {
                $classInfo = [
                    'admission_no'  => $student->admission_no,
                    'roll_no'       => $student->roll_no,
                    'standard_id'   => $student->standard_id,
                    'standard_name' => $student->standard->name ?? null,
                    'section_id'    => $student->section_id,
                    'section_name'  => $student->section->name ?? null,
                    'class_display' => trim(
                        ($student->standard->name ?? '') .
                        ($student->section ? ' - ' . $student->section->name : '')
                    ) ?: null,
                ];
                $displayName  = $student->full_name ?? $user->name;
                $displayImage = $student->image ?? $user->image;
            }
        } elseif ($user->role === 'teacher') {
            $teacher = TeacherDetail::where('user_id', $user->id)->first();
            if ($teacher) {
                $classInfo = [
                    'employee_id'   => $teacher->employee_id,
                    'qualification' => $teacher->qualification,
                    'class_display' => 'Teacher' . ($teacher->employee_id ? ' · ' . $teacher->employee_id : ''),
                ];
            }
        }

        return [
            'user_id'      => $user->id,
            'name'         => $displayName,
            'username'     => $displayName,
            'email'        => $user->email,
            'role'         => $user->role,
            'role_label'   => $user->role === 'user' ? 'Student' : ucfirst($user->role),
            // Friendly account type for the switcher card: 'student' | 'teacher'
            'user_type'    => $user->role === 'teacher' ? 'teacher' : 'student',
            'image'        => $displayImage,
            'organization' => $organization ? [
                'id'   => $organization->id,
                'name' => $organization->name,
                'logo' => $organization->logo,
            ] : null,
            'class_info'   => $classInfo,
        ];
    }
}
