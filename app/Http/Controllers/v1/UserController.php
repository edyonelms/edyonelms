<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\UserRequest;
use App\Http\Resources\StudentProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Student\StudentDetail;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function studentLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admission_number' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseService->errorResponse(
                implode(', ', $validator->errors()->all()),
                400
            );
        }

        // Find student detail by admission number
        $studentDetail = StudentDetail::where('admission_no', $request->admission_number)->first();

        if (!$studentDetail) {
            return $this->responseService->error(
                'The provided admission number does not exist in our records.',
                401
            );
        }

        // Get the associated user
        $user = $studentDetail->user()->where('role', 'user')->first();

        if (!$user) {
            return $this->responseService->error(
                'No valid student account found for this admission number.',
                401
            );
        }

        if (!$user->is_active) {
            return $this->responseService->error(
                'Student account is not active. Please contact support.',
                403
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->responseService->error(
                'The provided password is incorrect.',
                401
            );
        }

        if (!$user->is_active) {
            return $this->responseService->error(
                'Your account has been deactivated. Please contact support.',
                403
            );
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;
        return $this->responseService->authResponse(
            new UserResource($user),
            $token,
            'Login successful'
        );
    }

    public function studentProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Load student details with relationships
            $studentDetail = StudentDetail::with(['user', 'standard', 'section'])
                ->where('user_id', $user->id)
                ->first();

            if (!$studentDetail) {
                return $this->responseService->errorResponse(
                    'Student profile not found',
                    404
                );
            }

            return $this->responseService->success(
                new StudentProfileResource($studentDetail),
                'Student profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                $e->getMessage(),
                500
            );
        }
    }
}
