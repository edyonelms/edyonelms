<?php

namespace App\Http\Controllers\v1;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SwitchAccountController extends ApiController
{
    /**
     * GET /api/v1/switch-account/schools
     *
     * Returns all schools the current user is registered in
     * (matched by email across organizations).
     *
     * Use case: a user (student or teacher) enrolled in multiple schools
     * can see all their accounts and pick which one to switch to.
     */
    public function schools()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        // Find all user records with the same email (across orgs)
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
     * Revokes the current token, issues a new one for the target organization's
     * user record (same email + role, different org).
     *
     * Body: { "organization_id": 5 }
     */
    public function switch(\Illuminate\Http\Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $validationErr = $this->validateWith($request, [
            'organization_id' => 'required|integer|exists:organizations,id',
        ]);
        if ($validationErr) return $validationErr;

        $targetOrgId = (int) $request->organization_id;

        // Already on this org
        if ($user->organization_id === $targetOrgId) {
            return $this->error('You are already logged in to this school.', 400);
        }

        // Find the same user (same email + role) in the target org
        $targetUser = User::where('email', $user->email)
            ->where('role', $user->role)
            ->where('organization_id', $targetOrgId)
            ->where('is_active', true)
            ->first();

        if (!$targetUser) {
            return $this->error('No account found for your email in the selected school.', 404);
        }

        // Revoke current token
        $user->currentAccessToken()->delete();

        // Issue new token for target user
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
}
