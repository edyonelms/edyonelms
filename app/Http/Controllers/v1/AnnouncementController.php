<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    protected ResponseService $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function announcementList()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            // Get announcements for the user's organization from last 30 days
            $query = Announcement::where('organization_id', $user->organization_id)
                ->where('created_at', '>=', now()->subDays(30))
                ->orderBy('created_at', 'desc');

            // Filter based on type if provided in request
            if (request()->has('type')) {
                $type = request()->input('type');

                if ($type === 'all') {
                    // No type filter needed - show all
                } elseif ($type === 'user_all') {
                    // Show both 'all' and 'user' type announcements
                    $query->whereIn('type', ['all', 'user']);
                } elseif ($type === 'teacher_all') {
                    // Show both 'all' and 'teacher' type announcements
                    $query->whereIn('type', ['all', 'teacher']);
                } else {
                    // Filter by specific type
                    $query->where('type', $type);
                }
            } else {
                // Default behavior based on user type
                if ($user->user_type === 'teacher') {
                    $query->whereIn('type', ['all', 'teacher']);
                } else {
                    $query->whereIn('type', ['all', 'user']);
                }
            }

            // Get the last 30 announcements
            $announcements = $query->limit(30)
                ->get()
                ->map(function ($announcement) {
                    $announcementData = $announcement->toArray();

                    // Add creator details if user exists
                    if ($announcement->user) {
                        $announcementData['creator_name'] = $announcement->user->name;
                        $announcementData['creator_email'] = $announcement->user->email;
                    } else {
                        $announcementData['creator_name'] = 'Unknown';
                        $announcementData['creator_email'] = null;
                    }

                    // Add full URLs for files
                    $announcementData['image_url'] = $announcement->announcement_image
                        ? Storage::disk('s3')->url($announcement->announcement_image)
                        : null;

                    $announcementData['pdf_url'] = $announcement->announcement_pdf
                        ? Storage::disk('s3')->url($announcement->announcement_pdf)
                        : null;

                    return $announcementData;
                });

            return $this->responseService->success(
                $announcements,
                'Announcement list retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getAnnouncement($id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse(
                    'Authentication required',
                    401
                );
            }

            $announcement = Announcement::where('organization_id', $user->organization_id)
                ->where('id', $id)
                ->first();

            if (!$announcement) {
                return $this->responseService->errorResponse(
                    'Announcement not found or you dont have access',
                    404
                );
            }

            // Check if user has access to this announcement type
            $allowedTypes = ['all', $user->role];
            if (!in_array($announcement->type, $allowedTypes)) {
                return $this->responseService->errorResponse(
                    'You dont have access to this announcement',
                    403
                );
            }

            $announcementData = $announcement->toArray();

            // Add creator details
            if ($announcement->user) {
                $announcementData['creator_name'] = $announcement->user->name;
                $announcementData['creator_email'] = $announcement->user->email;
                $announcementData['creator_avatar'] = $announcement->user->avatar_url
                    ? Storage::disk('s3')->url($announcement->user->avatar_url)
                    : null;
            } else {
                $announcementData['creator_name'] = 'Unknown';
                $announcementData['creator_email'] = null;
                $announcementData['creator_avatar'] = null;
            }

            // Add full URLs for files
            $announcementData['image_url'] = $announcement->announcement_image
                ? Storage::disk('s3')->url($announcement->announcement_image)
                : null;

            $announcementData['pdf_url'] = $announcement->announcement_pdf
                ? Storage::disk('s3')->url($announcement->announcement_pdf)
                : null;

            return $this->responseService->success(
                $announcementData,
                'Announcement retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }
}
