<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Admin\HomeWork;
use App\Models\Student\StudentDetail;
use App\Services\ResponseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeWorkController extends Controller
{
    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    // Create homework
    public function uploadHomeWork(Request $request)
    {
        DB::beginTransaction();
        try {
            $homework = HomeWork::create([
                'organization_id' => Auth::user()->organization_id,
                'user_id' => Auth::id(),
                'standard_id' => $request->standard_id,
                'section_id' => $request->section_id,
                'subject_id' => $request->subject_id,
                'title' => $request->name,
                'description' => $request->description
            ]);

            DB::commit();
            return $this->responseService->success(
                $homework,
                'Homework created successfully'
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseService->errorResponse(
                'Failed to create homework: ' . $e->getMessage(),
                500
            );
        }
    }

    // Update homework
    public function updateHomeWork(Request $request, $chapterId)
    {
        try {
            $homework = HomeWork::where('organization_id', Auth::user()->organization_id)
                ->findOrFail($chapterId);

            $homework->update([
                'standard_id' => $request->standard_id,
                'section_id' => $request->section_id,
                'subject_id' => $request->subject_id,
                'title' => $request->name,
                'description' => $request->description
            ]);

            return $this->responseService->success(
                $homework,
                'Homework updated successfully'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->responseService->errorResponse('homework not found', 404);
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to update homework: ' . $e->getMessage(),
                500
            );
        }
    }

    // Delete homework
    public function destroyHomeWork($chapterId)
    {
        DB::beginTransaction();
        try {
            $homework = HomeWork::where('organization_id', Auth::user()->organization_id)
                ->findOrFail($chapterId);

            $homework->delete();
            DB::commit();

            return $this->responseService->success(
                [],
                'Homework deleted successfully'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return $this->responseService->errorResponse('homework not found', 404);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->responseService->errorResponse(
                'Failed to delete homework: ' . $e->getMessage(),
                500
            );
        }
    }

    // Get Single homework
    public function showSingleHomeWork($homeworkId)
    {
        try {
            $homework = HomeWork::with(['standard', 'section', 'subject'])
                ->where('organization_id', Auth::user()->organization_id)
                ->findOrFail($homeworkId);

            return $this->responseService->success(
                $homework,
                'homework retrieved successfully'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->responseService->errorResponse('homework not found', 404);
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to retrieve homework: ' . $e->getMessage(),
                500
            );
        }
    }

    // List All HomeWork with Pagination and Search
    public function allHomeWork(Request $request)
    {
        try {
            $query = HomeWork::with(['standard', 'section', 'subject'])
                ->where('organization_id', Auth::user()->organization_id);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('standard', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('section', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('subject', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            // Filter by standard_id if provided
            if ($request->has('standard_id')) {
                $query->where('standard_id', $request->standard_id);
            }

            // Filter by section_id if provided
            if ($request->has('section_id')) {
                $query->where('section_id', $request->section_id);
            }

            // Filter by subject_id if provided
            if ($request->has('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            // Sorting
            $allowedSortFields = ['created_at', 'title', 'updated_at'];
            $sortField = in_array($request->get('sort_field'), $allowedSortFields) ? $request->get('sort_field') : 'created_at';
            $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $homeworks = $query->paginate($perPage);

            return $this->responseService->success(
                $homeworks,
                'Homeworks retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to retrieve homeworks: ' . $e->getMessage(),
                500
            );
        }
    }

    public function studentHomeWork(Request $request)
    {
        try {
            $user = Auth::user();
            $organizationId = $user->organization_id;

            // Get student details
            $studentDetail = StudentDetail::where('user_id', $user->id)
                ->where('organization_id', $organizationId)
                ->first();

            if (!$studentDetail) {
                return $this->responseService->errorResponse('Student details not found', 404);
            }

            // Build query for student's homework
            $query = HomeWork::with(['standard', 'section', 'subject', 'user'])
                ->where('organization_id', $organizationId)
                ->where('standard_id', $studentDetail->standard_id)
                ->where('section_id', $studentDetail->section_id);

            // Search functionality
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('subject', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            // Filter by subject if provided
            if ($request->has('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            // Filter by date range
            if ($request->has('from_date') && $request->has('to_date')) {
                $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
            } elseif ($request->has('from_date')) {
                $query->where('created_at', '>=', $request->from_date);
            } elseif ($request->has('to_date')) {
                $query->where('created_at', '<=', $request->to_date);
            }

            // Show recent homework (last 7 days) by default
            if (!$request->has('from_date') && !$request->has('to_date') && !$request->has('search')) {
                $query->where('created_at', '>=', now()->subDays(7));
            }

            // Sorting
            $allowedSortFields = ['created_at', 'title', 'updated_at'];
            $sortField = in_array($request->get('sort_field'), $allowedSortFields) ? $request->get('sort_field') : 'created_at';
            $sortDirection = $request->get('sort_direction') === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 10);
            $homeworks = $query->paginate($perPage);

            // Transform the response
            $transformedHomeworks = $homeworks->map(function ($homework) {
                return [
                    'id' => $homework->id,
                    'title' => $homework->title,
                    'description' => $homework->description,
                    'subject' => $homework->subject ? [
                        'id' => $homework->subject->id,
                        'name' => $homework->subject->name,
                        'code' => $homework->subject->code ?? null,
                    ] : null,
                    'assigned_by' => $homework->user ? $homework->user->name : 'Unknown',
                    'assigned_date' => $homework->created_at->format('Y-m-d'),
                    'assigned_time' => $homework->created_at->format('h:i A'),
                    'days_ago' => $homework->created_at->diffForHumans(),
                    'standard' => $homework->standard->name ?? null,
                    'section' => $homework->section->name ?? null,
                ];
            });

            return $this->responseService->success(
                [
                    'student_info' => [
                        'id' => $studentDetail->id,
                        'name' => $studentDetail->full_name ?? $user->name,
                        'standard' => $studentDetail->standard->name ?? null,
                        'section' => $studentDetail->section->name ?? null,
                        'roll_no' => $studentDetail->roll_no,
                    ],
                    'homeworks' => $transformedHomeworks,
                    'pagination' => [
                        'current_page' => $homeworks->currentPage(),
                        'last_page' => $homeworks->lastPage(),
                        'per_page' => $homeworks->perPage(),
                        'total' => $homeworks->total(),
                    ],
                    'summary' => [
                        'total_homework' => $homeworks->total(),
                        'this_week' => HomeWork::where('organization_id', $organizationId)
                            ->where('standard_id', $studentDetail->standard_id)
                            ->where('section_id', $studentDetail->section_id)
                            ->where('created_at', '>=', now()->startOfWeek())
                            ->count(),
                        'pending_count' => 0, // You can add submission tracking later
                        'submitted_count' => 0, // You can add submission tracking later
                    ]
                ],
                'Student homework retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to retrieve student homework: ' . $e->getMessage(),
                500
            );
        }
    }
}
