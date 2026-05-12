<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\AboutApp;
use App\Models\Admin\RulesAndRegulation;
use App\Models\Admin\SchoolInfo;
use App\Models\Student\Chapter;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\Subject;
use App\Models\Student\Topic;
use App\Models\User;
use App\Services\OtplessService;
use App\Services\OtpMailService;
use App\Services\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $responseService;

    public function unauthenticate()
    {
        $responseArray = [
            'status' => false,
            'message' => 'Your are not logged in',
            'status_code' => 403,
        ];
        return response()->json($responseArray, 403);
    }

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->responseService->errorResponse(
                'No account found with this email address.',
                404
            );
        }

        try {
            OtpMailService::sendOtp($user, 'Student App');

            return $this->responseService->success(
                ['user_id' => $user->id],
                'OTP sent successfully to your email address.'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to send OTP: ' . $e->getMessage(),
                500
            );
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'otp'     => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return $this->responseService->errorResponse('User not found.', 404);
        }

        try {
            OtpMailService::verifyOtp($user, (string) $request->otp);
        } catch (Exception $e) {
            return $this->responseService->errorResponse($e->getMessage(), 401);
        }

        return $this->responseService->success(
            ['user_id' => $user->id],
            'OTP verified successfully.'
        );
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return $this->responseService->errorResponse('User not found.', 404);
        }

        if (!OtpMailService::canResend($user)) {
            $otpCreatedAt = \Carbon\Carbon::parse($user->otp_expires_at)->subMinutes(2);
            $remaining    = max(0, 120 - (int) now()->diffInSeconds($otpCreatedAt));

            return $this->responseService->errorResponse(
                "Please wait {$remaining} seconds before requesting a new OTP.",
                429
            );
        }

        try {
            OtpMailService::sendOtp($user, 'Student App');

            return $this->responseService->success(
                null,
                'OTP resent successfully to your email address.'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to resend OTP: ' . $e->getMessage(),
                500
            );
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/', 'confirmed'],
            'user_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        try {
            $user = User::find($request->user_id);
            if (!$user) {
                return $this->responseService->errorResponse(
                    'User not found',
                    404
                );
            }

            if ($user->otp_expires_at !== null) {
                return $this->responseService->errorResponse(
                    'OTP not verified',
                    400
                );
            }

            $user->password = Hash::make($request->password);
            $user->save();

            $token = $user->createToken('authToken')->plainTextToken;
            $token = explode('|', $token)[1];

            return $this->responseService->authResponse(
                new UserResource($user),
                $token,
                'Password updated successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed'
            ]
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.regex' => 'New password must contain at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        try {
            // Get authenticated user using Auth facade
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->responseService->errorResponse(
                    'Current password is incorrect',
                    401
                );
            }

            // Check if new password is same as current
            if (strcmp($request->current_password, $request->new_password) === 0) {
                return $this->responseService->errorResponse(
                    'New password must be different from current password',
                    400
                );
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Revoke all tokens and create new one
            $user->tokens()->delete();
            $token = $user->createToken('authToken')->plainTextToken;
            $token = explode('|', $token)[1];

            return $this->responseService->authResponse(
                new UserResource($user),
                $token,
                'Password updated successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function schoolInfo()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $schoolInfo = SchoolInfo::with(['managementTeam', 'documents', 'organization'])
                ->where('organization_id', $user->organization_id)
                ->first();

            if (!$schoolInfo) {
                return $this->responseService->errorResponse(
                    'School information not found',
                    404
                );
            }

            $schoolData = $schoolInfo->toArray();

            // Add full URLs for management team photos
            $schoolData['management_team'] = $schoolInfo->managementTeam->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'designation' => $member->designation,
                    'photo_url' => $member->photo_path,
                    'sort_order' => $member->sort_order
                ];
            });

            // Add full URLs for documents
            $schoolData['documents'] = $schoolInfo->documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'file_url' => $document->file_path,
                    'file_type' => $document->file_type,
                    'sort_order' => $document->sort_order
                ];
            });

            $schoolData['organization'] = [
                'logo_url' => $schoolInfo->organization->logo,
                'name' => $schoolInfo->organization->name
            ];

            return $this->responseService->success(
                $schoolData,
                'School information retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function aboutApp()
    {
        try {
            $aboutInfo = AboutApp::first();
            if (!$aboutInfo) {
                return $this->responseService->errorResponse(
                    'App information not found',
                    404
                );
            }

            return $this->responseService->success(
                $aboutInfo,
                'App information retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function rulesAndRegulations(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $rules = RulesAndRegulation::where('organization_id', $user->organization_id)->first();

            if (!$rules) {
                return $this->responseService->errorResponse(
                    'Rules and Regulations not found',
                    404
                );
            }

            return $this->responseService->success(
                $rules,
                'Rules and Regulations retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getCompleteCurriculumSimple()
    {
        try {
            $user = Auth::user();
            $organizationId = $user->organization_id;

            if (!$user || !$organizationId) {
                return $this->responseService->errorResponse(
                    'Authentication required or user not in organization',
                    401
                );
            }

            // Get all data separately and build hierarchy manually
            $standards = Standard::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            $sections = Section::whereIn('standard_id', $standards->pluck('id'))
                ->where('is_active', true)
                ->get();

            $subjects = Subject::where('organization_id', $organizationId)
                ->where('is_active', true)
                ->get();

            // Get all chapters with their topics
            $chapters = Chapter::where('organization_id', $organizationId)
                ->with('topics')
                ->orderBy('order')
                ->get();

            // Get standard-subject relationships
            $standardSubjects = DB::table('standard_subjects')
                ->whereIn('standard_id', $standards->pluck('id'))
                ->get();

            // Get section-subject relationships
            $sectionSubjects = DB::table('section_subjects')
                ->whereIn('section_id', $sections->pluck('id'))
                ->get();

            // Build curriculum hierarchy
            $curriculum = $standards->map(function ($standard) use ($sections, $subjects, $chapters, $standardSubjects, $sectionSubjects) {

                // Standard level subjects
                $standardSubjectIds = $standardSubjects->where('standard_id', $standard->id)->pluck('subject_id');
                $standardSubjectsData = $subjects->whereIn('id', $standardSubjectIds);

                $standardSubjectsWithChapters = $standardSubjectsData->map(function ($subject) use ($standard, $chapters, $standardSubjects) {
                    $subjectChapters = $chapters->where('subject_id', $subject->id)
                        ->where('standard_id', $standard->id)
                        ->whereNull('section_id')
                        ->values();

                    $pivotData = $standardSubjects->where('standard_id', $standard->id)
                        ->where('subject_id', $subject->id)
                        ->first();

                    return [
                        'subject_id' => $subject->id,
                        'subject_name' => $subject->name,
                        'subject_code' => $subject->code,
                        'description' => $subject->description,
                        'is_active' => (bool)$subject->is_active,
                        'is_mandatory' => $pivotData ? (bool)$pivotData->is_mandatory : true,
                        'chapters' => $subjectChapters->map(function ($chapter) {
                            return $this->transformChapter($chapter);
                        })
                    ];
                });

                // Sections with their subjects
                $standardSections = $sections->where('standard_id', $standard->id)->map(function ($section) use ($subjects, $chapters, $sectionSubjects, $standard) {
                    $sectionSubjectIds = $sectionSubjects->where('section_id', $section->id)->pluck('subject_id');
                    $sectionSubjectsData = $subjects->whereIn('id', $sectionSubjectIds);

                    $sectionSubjectsWithChapters = $sectionSubjectsData->map(function ($subject) use ($section, $chapters, $standard) {
                        $subjectChapters = $chapters->where('subject_id', $subject->id)
                            ->where('standard_id', $standard->id)
                            ->where('section_id', $section->id)
                            ->values();

                        return [
                            'subject_id' => $subject->id,
                            'subject_name' => $subject->name,
                            'subject_code' => $subject->code,
                            'description' => $subject->description,
                            'is_active' => (bool)$subject->is_active,
                            'is_mandatory' => true,
                            'chapters' => $subjectChapters->map(function ($chapter) {
                                return $this->transformChapter($chapter);
                            })
                        ];
                    });

                    return [
                        'section_id' => $section->id,
                        'section_name' => $section->name,
                        'image' => $section->image,
                        'description' => $section->description,
                        'is_active' => (bool)$section->is_active,
                        'subjects' => $sectionSubjectsWithChapters
                    ];
                });

                return [
                    'standard_id' => $standard->id,
                    'standard_name' => $standard->name,
                    'standard_code' => $standard->code,
                    'board' => $standard->board,
                    'order' => $standard->order,
                    'is_active' => (bool)$standard->is_active,
                    'sections' => $standardSections,
                    'subjects' => $standardSubjectsWithChapters
                ];
            });

            return $this->responseService->success(
                [
                    'data' => $curriculum
                ],
                'Complete curriculum retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    // Helper method to transform chapter data
    private function transformChapter($chapter)
    {
        return [
            'chapter_id' => $chapter->id,
            'chapter_name' => $chapter->name,
            'description' => $chapter->description,
            'image_path' => $chapter->image_path,
            'pdf_path' => $chapter->pdf_path,
            'content_type' => $chapter->content_type,
            'file_path' => $chapter->file_path,
            'thumbnail' => $chapter->thumbnail,
            'duration' => $chapter->duration,
            'order' => $chapter->order,
            'is_free' => (bool)$chapter->is_free,
            'is_published' => (bool)$chapter->is_published,
            'metadata' => $chapter->metadata,
            'section_id' => $chapter->section_id,
            'standard_id' => $chapter->standard_id,
            'subject_id' => $chapter->subject_id,
            'topics' => $chapter->topics->map(function ($topic) {
                return [
                    'topic_id' => $topic->id,
                    'topic_name' => $topic->topic_name,
                    'topic_content' => $topic->topic_content,
                    'image_path' => $topic->image_path,
                    'pdf_path' => $topic->pdf_path,
                    'chapter_id' => $topic->chapter_id
                ];
            })
        ];
    }
}
