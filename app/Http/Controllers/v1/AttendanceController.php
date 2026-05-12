<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Student\{StudentAttendance, StudentDetail};
use App\Models\Teacher\{AssignTeacherStandard, TeacherAttendance, TeacherDetail};
use App\Services\ResponseService;
use App\Services\StudentAttendanceService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    protected $responseService;
    protected $attendanceService;

    public function __construct(StudentAttendanceService $attendanceService, ResponseService $responseService)
    {
        $this->responseService = $responseService;
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get students for attendance marking
     */
    public function getStudentsForAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse('Authentication required', 401);
            }

            // Get teacher detail with eager loading
            $teacherDetail = TeacherDetail::with(['assignedClasses.standard', 'assignedClasses.section'])
                ->where('user_id', $user->id)
                ->first();

            if (!$teacherDetail) {
                return $this->responseService->errorResponse('Teacher profile not found', 404);
            }

            // Check if teacher has any assigned classes
            if ($teacherDetail->assignedClasses->isEmpty()) {
                return $this->responseService->errorResponse(
                    'No classes assigned to this teacher. Please contact administrator.',
                    404
                );
            }

            $date = $request->date ?? now()->toDateString();

            // Get students for each assigned class
            $studentsByClass = collect();

            foreach ($teacherDetail->assignedClasses as $assignment) {
                $students = StudentDetail::with(['user', 'standard', 'section'])
                    ->where('organization_id', $user->organization_id)
                    ->where('standard_id', $assignment->standard_id);

                if ($assignment->section_id) {
                    $students->where('section_id', $assignment->section_id);
                }

                $students = $students->orderBy('roll_no')->get();

                // Get attendance for each student for the date
                $studentIds = $students->pluck('id')->toArray();
                $attendances = StudentAttendance::whereIn('student_detail_id', $studentIds)
                    ->where('attendance_date', $date)
                    ->get()
                    ->keyBy('student_detail_id');

                $classStudents = $students->map(function ($student) use ($attendances, $assignment) {
                    $attendance = $attendances->get($student->id);

                    return [
                        'student_id' => $student->id,
                        'user_id' => $student->user_id,
                        'roll_no' => $student->roll_no,
                        'full_name' => $student->full_name,
                        'photo' => $student->user->image ?? null,
                        'standard_id' => $student->standard_id,
                        'section_id' => $student->section_id,
                        'standard_name' => $student->standard->name ?? null,
                        'section_name' => $student->section->name ?? null,
                        'attendance' => $attendance ? [
                            'attendance_id' => $attendance->id,
                            'status' => $this->getAttendanceStatus($attendance->status),
                            'db_status' => $attendance->status,
                            'remarks' => $attendance->remarks,
                            'marked_by' => $attendance->marked_by,
                            'marked_at' => $attendance->created_at
                        ] : [
                            'status' => 'not_marked',
                            'db_status' => null,
                            'remarks' => null
                        ]
                    ];
                });

                $studentsByClass->push([
                    'assignment_id' => $assignment->id,
                    'class_info' => [
                        'standard_id' => $assignment->standard_id,
                        'standard_name' => $assignment->standard->name ?? null,
                        'section_id' => $assignment->section_id,
                        'section_name' => $assignment->section->name ?? null,
                        'class_display' => ($assignment->standard->name ?? '') .
                            ($assignment->section ? ' - ' . $assignment->section->name : '')
                    ],
                    'total_students' => $classStudents->count(),
                    'students' => $classStudents
                ]);
            }

            $response = [
                'teacher_info' => [
                    'teacher_id' => $teacherDetail->id,
                    'name' => $teacherDetail->user->name,
                    'employee_id' => $teacherDetail->employee_id
                ],
                'date' => $date,
                'classes' => $studentsByClass,
                'summary' => [
                    'total_classes' => $studentsByClass->count(),
                    'total_students' => $studentsByClass->sum('total_students'),
                    'attendance_date' => $date
                ]
            ];

            return $this->responseService->success(
                $response,
                'Students retrieved successfully for attendance marking'
            );
        } catch (\Exception $e) {
            return $this->responseService->errorResponse(
                'Failed to retrieve students: ' . $e->getMessage(),
                500
            );
        }
    }

    private function getAttendanceStatus($statusCode)
    {
        $statusMap = [
            0 => 'absent',
            1 => 'present',
            2 => 'late',
            3 => 'half_day',
            4 => 'holiday'
        ];

        return $statusMap[$statusCode] ?? 'not_marked';
    }

    /**
     * Bulk submit attendance
     */
    public function bulkSubmitAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return $this->responseService->errorResponse('Authentication required', 401);
            }

            $validated = $request->validate([
                'attendance_date' => 'required|date',
                'attendances' => 'required|array|min:1',
                'attendances.*.student_detail_id' => 'required|exists:student_details,id',
                'attendances.*.status' => 'required|boolean',
                'attendances.*.remarks' => 'nullable|string'
            ]);

            $results = $this->attendanceService->bulkSubmitAttendance(
                $validated,
                $user->id,
                $user->organization_id
            );

            // Get summary for the day
            $firstStudent = StudentDetail::find($validated['attendances'][0]['student_detail_id']);
            $summary = $this->attendanceService->getDailyAttendanceSummary(
                $user->organization_id,
                $firstStudent->standard_id,
                $firstStudent->section_id,
                $validated['attendance_date']
            );

            return $this->responseService->success([
                'processed_count' => count($results),
                'summary' => $summary,
                'details' => $results
            ], 'Attendance submitted successfully');
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get attendance summary for a class
     */
    public function getAttendanceSummary(Request $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validate([
                'date' => 'required|date',
                'standard_id' => 'sometimes|exists:standards,id',
                'section_id' => 'sometimes|exists:sections,id'
            ]);

            $teacherAssignments = AssignTeacherStandard::where('teacher_detail_id', $user->teacherDetail->id)
                ->where('organization_id', $user->organization_id);

            // Agar specific class request ki hai
            if (isset($validated['standard_id']) && isset($validated['section_id'])) {
                // Verify teacher is assigned to this class
                $isAssigned = $teacherAssignments->where('standard_id', $validated['standard_id'])
                    ->where('section_id', $validated['section_id'])
                    ->exists();

                if (!$isAssigned) {
                    return $this->responseService->errorResponse('You are not assigned to this class', 403);
                }

                $summary = $this->attendanceService->getDailyAttendanceSummary(
                    $user->organization_id,
                    $validated['standard_id'],
                    $validated['section_id'],
                    $validated['date']
                );

                return $this->responseService->success([$summary], 'Attendance summary retrieved successfully');
            }

            $assignedClasses = $teacherAssignments->get();
            $summaries = [];

            foreach ($assignedClasses as $class) {
                $summary = $this->attendanceService->getDailyAttendanceSummary(
                    $user->organization_id,
                    $class->standard_id,
                    $class->section_id,
                    $validated['date']
                );

                $summaries[] = [
                    'standard_id' => $class->standard_id,
                    'section_id' => $class->section_id,
                    'standard_name' => $class->standard->name,
                    'section_name' => $class->section->name,
                    ...$summary
                ];
            }

            return $this->responseService->success($summaries, 'All classes attendance summary retrieved successfully');
        } catch (Exception $e) {
            return $this->responseService->errorResponse('An error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function teacherAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            // Get teacher detail
            $teacherDetail = TeacherDetail::where('user_id', $user->id)->first();

            if (!$teacherDetail) {
                return $this->responseService->errorResponse(
                    'Teacher profile not found',
                    404
                );
            }

            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'month' => 'nullable|integer|min:1|max:12',
                'year' => 'nullable|integer|min:2000|max:' . date('Y'),
                'date' => 'nullable|date',
                'status' => 'nullable|in:present,absent,late,half_day',
                'per_page' => 'nullable|integer|min:5|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return $this->responseService->errorResponse(
                    'Validation failed',
                    422,
                );
            }

            // Build query
            $query = TeacherAttendance::with(['recordedBy'])
                ->where('teacher_detail_id', $teacherDetail->id)
                ->where('organization_id', $user->organization_id)
                ->orderBy('attendance_date', 'desc');

            // Apply filters
            if ($request->filled('date')) {
                $query->whereDate('attendance_date', $request->date);
            }

            if ($request->filled('month') && $request->filled('year')) {
                $query->whereMonth('attendance_date', $request->month)
                    ->whereYear('attendance_date', $request->year);
            } elseif ($request->filled('month')) {
                $query->whereMonth('attendance_date', $request->month)
                    ->whereYear('attendance_date', date('Y'));
            } elseif ($request->filled('year')) {
                $query->whereYear('attendance_date', $request->year);
            }

            if ($request->filled('status')) {
                $statusMap = [
                    'present' => 1,
                    'absent' => 0,
                    'late' => 2,
                    'half_day' => 3
                ];
                $query->where('status', $statusMap[$request->status] ?? 1);
            }

            // Get paginated results
            $perPage = $request->per_page ?? 20;
            $attendance = $query->paginate($perPage);

            // Format response
            $formattedData = $attendance->map(function ($record) {
                return [
                    'id' => $record->id,
                    'attendance_date' => $record->attendance_date->format('Y-m-d'),
                    'day_name' => $record->attendance_date->format('l'),
                    'status' => $record->getStatusLabelAttribute(),
                    'status_code' => $record->status,
                    'remarks' => $record->remarks,
                    'marked_by' => $record->recordedBy ? [
                        'id' => $record->recordedBy->id,
                        'name' => $record->recordedBy->name,
                        'role' => $record->recordedBy->role
                    ] : null,
                    'marked_at' => $record->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $record->updated_at->format('Y-m-d H:i:s')
                ];
            });

            // Calculate statistics
            $statistics = $this->getTeacherAttendanceStatistics($teacherDetail->id, $user->organization_id);

            // Prepare pagination metadata
            $paginationData = [
                'current_page' => $attendance->currentPage(),
                'last_page' => $attendance->lastPage(),
                'per_page' => $attendance->perPage(),
                'total' => $attendance->total(),
                'from' => $attendance->firstItem(),
                'to' => $attendance->lastItem(),
                'has_more_pages' => $attendance->hasMorePages(),
                'next_page_url' => $attendance->nextPageUrl(),
                'prev_page_url' => $attendance->previousPageUrl()
            ];

            $response = [
                'teacher_info' => [
                    'teacher_id' => $teacherDetail->id,
                    'name' => $teacherDetail->user->name,
                    'employee_id' => $teacherDetail->employee_id,
                    'joining_date' => $teacherDetail->date_of_joining
                ],
                'attendance_records' => $formattedData,
                'statistics' => $statistics,
                'pagination' => $paginationData,
                'filters' => [
                    'applied_filters' => $request->only(['month', 'year', 'date', 'status']),
                    'available_filters' => [
                        'status_options' => ['present', 'absent', 'late', 'half_day'],
                        'month_range' => [
                            '1' => 'January',
                            '2' => 'February',
                            '3' => 'March',
                            '4' => 'April',
                            '5' => 'May',
                            '6' => 'June',
                            '7' => 'July',
                            '8' => 'August',
                            '9' => 'September',
                            '10' => 'October',
                            '11' => 'November',
                            '12' => 'December'
                        ]
                    ]
                ]
            ];

            return $this->responseService->success(
                $response,
                'Teacher attendance records retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get teacher attendance statistics
     */
    private function getTeacherAttendanceStatistics($teacherId, $organizationId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Current month statistics
        $currentMonthStats = TeacherAttendance::where('teacher_detail_id', $teacherId)
            ->where('organization_id', $organizationId)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Overall statistics
        $overallStats = TeacherAttendance::where('teacher_detail_id', $teacherId)
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Last 30 days
        $last30Days = TeacherAttendance::where('teacher_detail_id', $teacherId)
            ->where('organization_id', $organizationId)
            ->where('attendance_date', '>=', now()->subDays(30))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Calculate percentages
        $totalCurrentMonth = array_sum($currentMonthStats->toArray());
        $totalOverall = array_sum($overallStats->toArray());

        return [
            'current_month' => [
                'present' => $currentMonthStats[1] ?? 0,
                'absent' => $currentMonthStats[0] ?? 0,
                'late' => $currentMonthStats[2] ?? 0,
                'half_day' => $currentMonthStats[3] ?? 0,
                'total' => $totalCurrentMonth,
                'present_percentage' => $totalCurrentMonth > 0 ? round(($currentMonthStats[1] ?? 0) / $totalCurrentMonth * 100, 2) : 0
            ],
            'last_30_days' => [
                'present' => $last30Days[1] ?? 0,
                'absent' => $last30Days[0] ?? 0,
                'late' => $last30Days[2] ?? 0,
                'half_day' => $last30Days[3] ?? 0,
                'total' => array_sum($last30Days->toArray())
            ],
            'overall' => [
                'present' => $overallStats[1] ?? 0,
                'absent' => $overallStats[0] ?? 0,
                'late' => $overallStats[2] ?? 0,
                'half_day' => $overallStats[3] ?? 0,
                'total' => $totalOverall,
                'present_percentage' => $totalOverall > 0 ? round(($overallStats[1] ?? 0) / $totalOverall * 100, 2) : 0
            ]
        ];
    }

    public function todaysAttendance(Request $request)
    {
        try {
            $user = Auth::user();

            $teacherDetail = TeacherDetail::where('user_id', $user->id)->first();

            if (!$teacherDetail) {
                return $this->responseService->errorResponse('Teacher not found', 404);
            }

            $today = now()->toDateString();

            $attendance = TeacherAttendance::with(['recordedBy'])
                ->where('teacher_detail_id', $teacherDetail->id)
                ->where('organization_id', $user->organization_id)
                ->whereDate('attendance_date', $today)
                ->first();

            $response = [
                'date' => $today,
                'day_name' => now()->format('l'),
                'attendance' => $attendance ? [
                    'status' => $attendance->getStatusLabelAttribute(),
                    'status_code' => $attendance->status,
                    'remarks' => $attendance->remarks,
                    'marked_by' => $attendance->recordedBy ? [
                        'name' => $attendance->recordedBy->name,
                        'role' => $attendance->recordedBy->role
                    ] : null,
                    'marked_at' => $attendance->created_at->format('h:i A'),
                    'can_edit' => $this->canEditAttendance($attendance)
                ] : [
                    'status' => 'not_marked',
                    'status_code' => null,
                    'remarks' => null,
                    'marked_by' => null,
                    'marked_at' => null,
                    'can_edit' => true
                ]
            ];

            return $this->responseService->success(
                $response,
                'Today\'s attendance retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                'An error occurred: ' . $e->getMessage(),
                500
            );
        }
    }

    private function canEditAttendance($attendance)
    {
        return $attendance->created_at->diffInHours(now()) < 24;
    }
}
