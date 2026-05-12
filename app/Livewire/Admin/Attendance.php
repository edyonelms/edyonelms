<?php

namespace App\Livewire\Admin;

use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentAttendance;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\AssignTeacherStandard;
use App\Models\Teacher\TeacherAttendance;
use App\Models\Teacher\TeacherDetail;
use App\Models\Teacher\TeacherSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Attendance extends Component
{
    use WireUiActions, WithPagination;

    public $activeTab = 'teacher_attendance';
    public $subTab = 'teacher_attendance';

    // For teacher assignment form
    public $showModalAssignTeacher = false;
    public $editId = null;
    public $teacher_detail_id;
    public $standard_id;
    public $section_id;

    // For teacher attendance
    public $teacher_attendance_date;
    public $teacher_attendance_data = [];

    // For student attendance
    public $student_attendance_date;
    public $selected_standard = '';
    public $selected_section = '';

    // For filtering
    public $filter_date;
    public $filter_teacher = '';
    public $filter_status = '';
    public $filteredSections = [];

    // Data collections
    public $teachers = [];
    public $standards = [];
    public $sections;
    public $assignments;

    public $tabs = [
        'teacher_attendance' => [
            'teacher_attendance' => 'Mark Attendance',
            'teacher_attendance_list' => 'Attendance List',
            'teacher_attendance_dashboard' => 'Dashboard',
        ],
        'student_attendance' => [
            'student_attendance_list' => 'Attendance List',
            'student_attendance_dashboard' => 'Dashboard',
        ],
        'assign_teacher_class' => [
            'assign_teacher' => 'Assign Teacher',
        ]
    ];

    public function mount()
    {
        $this->loadData();
        $this->teacher_attendance_date = now()->format('Y-m-d');
        $this->student_attendance_date = now()->format('Y-m-d');
        $this->filter_date = now()->format('Y-m-d');
        $this->loadTeacherAttendanceData();
    }

    protected function loadData()
    {
        $orgId = Auth::user()->organization_id;

        $this->standards = Standard::where('organization_id', $orgId)->get();
        $this->sections = Section::whereHas('standard', fn($q) => $q->where('organization_id', $orgId))->get();
        $this->teachers = $this->loadAvailableTeachers();
    }

    protected function loadAvailableTeachers()
    {
        $assignedTeacherIds = AssignTeacherStandard::where('organization_id', Auth::user()->organization_id)
            ->when($this->editId, function ($q) {
                $q->where('id', '!=', $this->editId);
            })
            ->pluck('teacher_detail_id')
            ->toArray();

        return TeacherDetail::with('user')
            ->where('organization_id', Auth::user()->organization_id)
            ->whereNotIn('id', $assignedTeacherIds)
            ->get();
    }

    // Teacher Assignment Methods
    public function updatedStandardId($value)
    {
        $this->filteredSections = Section::where('standard_id', $value)
            ->get()
            ->map(function ($section) {
                return [
                    'value' => $section->id,
                    'label' => $section->name,
                ];
            })
            ->toArray();

        $this->section_id = null;
    }

    public function openModalAssignTeacher()
    {
        $this->showModalAssignTeacher = true;
    }

    public function closeModalAssignTeacher()
    {
        $this->showModalAssignTeacher = false;
        $this->resetForm();
    }

    public function onEdit($id)
    {
        $this->editId = $id;
        $assignment = AssignTeacherStandard::find($id);

        $this->teacher_detail_id = $assignment->teacher_detail_id;
        $this->standard_id = $assignment->standard_id;
        $this->section_id = $assignment->section_id;

        $this->updatedStandardId($this->standard_id);
        $this->loadAvailableTeachers();
        $this->showModalAssignTeacher = true;
    }

    public function saveAssignment()
    {
        $this->validate([
            'teacher_detail_id' => 'required|exists:teacher_details,id',
            'standard_id' => 'required|exists:standards,id',
            'section_id' => [
                'integer',
                Rule::when($this->section_id != 0, 'exists:sections,id')
            ],
        ]);

        $existing = AssignTeacherStandard::where('teacher_detail_id', $this->teacher_detail_id)
            ->where('standard_id', $this->standard_id)
            ->when($this->section_id, function ($query) {
                $query->where('section_id', $this->section_id);
            })
            ->when($this->editId, function ($query) {
                $query->where('id', '!=', $this->editId);
            })
            ->exists();

        if ($existing) {
            $this->notification()->error(
                $this->section_id
                    ? 'This teacher is already assigned to this class-section!'
                    : 'This teacher is already assigned to this class!'
            );
            return;
        }

        try {
            DB::beginTransaction();

            $data = [
                'teacher_detail_id' => $this->teacher_detail_id,
                'standard_id' => $this->standard_id,
                'section_id' => $this->section_id,
                'organization_id' => Auth::user()->organization_id,
            ];

            if ($this->editId) {
                AssignTeacherStandard::find($this->editId)->update($data);
                $message = 'Assignment updated successfully!';
            } else {
                $assignment = AssignTeacherStandard::create($data);

                // Also create entry in TeacherSection table for consistency
                TeacherSection::create([
                    'teacher_detail_id' => $this->teacher_detail_id,
                    'standard_id' => $this->standard_id,
                    'section_id' => $this->section_id,
                    'organization_id' => Auth::user()->organization_id,
                ]);

                $message = 'Teacher assigned successfully!';
            }

            DB::commit();

            $this->closeModalAssignTeacher();
            $this->loadData();
            $this->notification()->success($message);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error('Error!', 'Failed to save assignment: ' . $e->getMessage());
        }
    }

    public function onDelete($id)
    {
        AssignTeacherStandard::find($id)->delete();
        $this->loadData();
        $this->notification()->success('Assignment deleted successfully!');
    }

    // Teacher Attendance Methods with Live Updates
    public function loadTeacherAttendanceData()
    {
        $teachers = TeacherDetail::with(['user', 'attendance' => function ($q) {
            $q->whereDate('attendance_date', $this->teacher_attendance_date);
        }])->where('organization_id', Auth::user()->organization_id)->get();

        $this->teacher_attendance_data = [];
        foreach ($teachers as $teacher) {
            $existingAttendance = $teacher->attendance->first();

            $dbStatus = $existingAttendance ? $existingAttendance->status : 1;
            $displayStatus = $this->getStatusLabel($dbStatus);

            $this->teacher_attendance_data[$teacher->id] = [
                'status' => $displayStatus,
                'db_status' => $dbStatus,
                'remarks' => $existingAttendance ? $existingAttendance->remarks : ''
            ];
        }
    }


    public function updatedTeacherAttendanceDate()
    {
        $this->loadTeacherAttendanceData();
    }
    public function updateTeacherAttendance($teacherId, $status)
    {
        try {
            $organizationId = Auth::user()->organization_id;
            $markedBy = Auth::id();

            // Convert status to database values
            $dbStatus = $this->getStatusValue($status);

            $attendance = TeacherAttendance::updateOrCreate(
                [
                    'teacher_detail_id' => $teacherId,
                    'organization_id' => $organizationId,
                    'attendance_date' => $this->teacher_attendance_date
                ],
                [
                    'status' => $dbStatus,
                    'remarks' => $this->teacher_attendance_data[$teacherId]['remarks'] ?? '',
                    'marked_by' => $markedBy
                ]
            );

            // Update local data with display status
            $this->teacher_attendance_data[$teacherId]['status'] = $status;
            $this->teacher_attendance_data[$teacherId]['db_status'] = $dbStatus;

            $this->dispatch('attendanceUpdated');

            // Reload data to reflect changes
            $this->loadTeacherAttendanceData();
        } catch (\Exception $e) {

            $this->notification()->error(
                'Error',
                'Failed to update attendance: ' . $e->getMessage()
            );
        }
    }

    protected function getStatusValue($status)
    {
        // Map frontend status to database values
        $statusMap = [
            'present' => 1,    // Present
            'absent' => 0,     // Absent
            'late' => 2,       // Late
            'half_day' => 3,   // Half Day
            'none' => 4
        ];

        return $statusMap[$status] ?? 4; // Default to present
    }

    // Reverse mapping for displaying status
    protected function getStatusLabel($dbStatus)
    {
        $statusMap = [
            1 => 'present',
            0 => 'absent',
            2 => 'late',
            3 => 'half_day',
            4 => 'none'
        ];

        return $statusMap[$dbStatus] ?? 'none';
    }

    public function updateTeacherRemarks($teacherId)
    {
        try {
            $organizationId = Auth::user()->organization_id;
            $markedBy = Auth::id();

            // Get current status from local data or default to present (1)
            $currentStatus = $this->teacher_attendance_data[$teacherId]['db_status'] ?? 1;

            TeacherAttendance::updateOrCreate(
                [
                    'teacher_detail_id' => $teacherId,
                    'organization_id' => $organizationId,
                    'attendance_date' => $this->teacher_attendance_date
                ],
                [
                    'status' => $currentStatus,
                    'remarks' => $this->teacher_attendance_data[$teacherId]['remarks'] ?? '',
                    'marked_by' => $markedBy
                ]
            );

            $this->dispatch('remarksUpdated');
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error',
                'Failed to update remarks: ' . $e->getMessage()
            );
        }
    }

    // Export Methods
    public function exportTeacherAttendance()
    {
        try {
            $data = TeacherAttendance::with(['teacherDetails.user'])
                ->whereDate('attendance_date', $this->filter_date)
                ->when($this->filter_teacher, function ($q) {
                    $q->where('teacher_detail_id', $this->filter_teacher);
                })
                ->when($this->filter_status, function ($q) {
                    $q->where('status', $this->filter_status);
                })
                ->get();

            // Export logic here
            $this->notification()->success(
                'Export Successful',
                'Teacher attendance data exported successfully.'
            );
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error',
                'Failed to export attendance: ' . $e->getMessage()
            );
        }
    }

    public function exportStudentAttendance()
    {
        try {
            $data = StudentAttendance::with(['studentDetail.user'])
                ->whereDate('attendance_date', $this->filter_date)
                ->when($this->selected_standard, function ($q) {
                    $q->whereHas('studentDetail', function ($q2) {
                        $q2->where('standard_id', $this->selected_standard);
                    });
                })
                ->when($this->filter_status, function ($q) {
                    $q->where('status', $this->filter_status);
                })
                ->get();

            // Export logic here
            $this->notification()->success(
                'Export Successful',
                'Student attendance data exported successfully.'
            );
        } catch (\Exception $e) {
            $this->notification()->error(
                'Error',
                'Failed to export attendance: ' . $e->getMessage()
            );
        }
    }

    // Dashboard Statistics - Teacher
    public function getTeacherStatsProperty()
    {
        $organizationId = Auth::user()->organization_id;

        $totalTeachers = TeacherDetail::where('organization_id', $organizationId)->count();

        // Use integer values for status comparison
        $todayStats = TeacherAttendance::whereDate('attendance_date', today())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $yesterdayStats = TeacherAttendance::whereDate('attendance_date', today()->subDay())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Use integer keys to access counts
        $presentToday = $todayStats[1] ?? 0; // 1 = present
        $absentToday = $todayStats[0] ?? 0;  // 0 = absent
        $lateToday = $todayStats[2] ?? 0;    // 2 = late
        $halfDayToday = $todayStats[3] ?? 0; // 3 = half_day

        $presentYesterday = $yesterdayStats[1] ?? 0;
        $presentGrowth = $this->calculateGrowth($presentToday, $presentYesterday);

        return [
            'total_teachers' => [
                'count' => $totalTeachers,
                'trend' => 'up',
                'growth' => 2.5
            ],
            'present_today' => [
                'count' => $presentToday,
                'trend' => $presentGrowth >= 0 ? 'up' : 'down',
                'growth' => abs($presentGrowth)
            ],
            'absent_today' => [
                'count' => $absentToday,
                'trend' => 'down',
                'growth' => 1.2
            ],
            'late_today' => [
                'count' => $lateToday,
                'trend' => 'down',
                'growth' => 0.8
            ],
            'half_day_today' => [
                'count' => $halfDayToday,
                'trend' => 'down',
                'growth' => 0.5
            ]
        ];
    }

    // Dashboard Statistics - Student
    public function getStudentStatsProperty()
    {
        $organizationId = Auth::user()->organization_id;

        $totalStudents = StudentDetail::where('organization_id', $organizationId)->count();

        // Use integer values for status comparison
        $todayStats = StudentAttendance::whereDate('attendance_date', today())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $yesterdayStats = StudentAttendance::whereDate('attendance_date', today()->subDay())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Use integer keys to access counts
        $presentToday = $todayStats[1] ?? 0; // 1 = present
        $absentToday = $todayStats[0] ?? 0;  // 0 = absent
        $lateToday = $todayStats[2] ?? 0;    // 2 = late
        $halfDayToday = $todayStats[3] ?? 0; // 3 = half_day

        $presentYesterday = $yesterdayStats[1] ?? 0;
        $presentGrowth = $this->calculateGrowth($presentToday, $presentYesterday);

        return [
            'total_students' => [
                'count' => $totalStudents,
                'trend' => 'up',
                'growth' => 3.2
            ],
            'present_today' => [
                'count' => $presentToday,
                'trend' => $presentGrowth >= 0 ? 'up' : 'down',
                'growth' => abs($presentGrowth)
            ],
            'absent_today' => [
                'count' => $absentToday,
                'trend' => 'down',
                'growth' => 2.1
            ],
            'late_today' => [
                'count' => $lateToday,
                'trend' => 'down',
                'growth' => 1.3
            ],
            'half_day_today' => [
                'count' => $halfDayToday,
                'trend' => 'down',
                'growth' => 0.9
            ]
        ];
    }

    protected function calculateGrowth($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    // Chart Data Methods - Teacher
    public function getTeacherAttendanceTrendData()
    {
        $organizationId = Auth::user()->organization_id;
        $months = [];
        $presentData = [];
        $absentData = [];

        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            $months[] = $startDate->format('M Y');

            // Get actual data for the month
            $monthStats = TeacherAttendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->where('organization_id', $organizationId)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            $presentData[] = $monthStats[1] ?? 0; // Present count
            $absentData[] = $monthStats[0] ?? 0;  // Absent count
        }

        return [
            'months' => $months,
            'present' => $presentData,
            'absent' => $absentData
        ];
    }

    public function getTeacherStatusDistributionData()
    {
        $organizationId = Auth::user()->organization_id;

        $todayStats = TeacherAttendance::whereDate('attendance_date', today())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'Present' => $todayStats[1] ?? 0,      // 1 = present
            'Absent' => $todayStats[0] ?? 0,       // 0 = absent
            'Late' => $todayStats[2] ?? 0,         // 2 = late
            'Half Day' => $todayStats[3] ?? 0      // 3 = half_day
        ];
    }

    // Chart Data Methods - Student
    public function getStudentAttendanceTrendData()
    {
        $organizationId = Auth::user()->organization_id;
        $months = [];
        $presentData = [];
        $absentData = [];

        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            $months[] = $startDate->format('M Y');

            // Get actual data for the month
            $monthStats = StudentAttendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->where('organization_id', $organizationId)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');

            $presentData[] = $monthStats[1] ?? 0; // Present count
            $absentData[] = $monthStats[0] ?? 0;  // Absent count
        }

        return [
            'months' => $months,
            'present' => $presentData,
            'absent' => $absentData
        ];
    }

    public function getStudentStatusDistributionData()
    {
        $organizationId = Auth::user()->organization_id;

        $todayStats = StudentAttendance::whereDate('attendance_date', today())
            ->where('organization_id', $organizationId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'Present' => $todayStats[1] ?? 0,      // 1 = present
            'Absent' => $todayStats[0] ?? 0,       // 0 = absent
            'Late' => $todayStats[2] ?? 0,         // 2 = late
            'Half Day' => $todayStats[3] ?? 0      // 3 = half_day
        ];
    }

    public function getClassWiseAttendanceData()
    {
        $organizationId = Auth::user()->organization_id;
        $distribution = [];

        // Get all standards
        $standards = Standard::where('organization_id', $organizationId)->get();

        foreach ($standards as $standard) {
            $totalStudents = StudentDetail::where('standard_id', $standard->id)
                ->where('organization_id', $organizationId)
                ->count();

            if ($totalStudents > 0) {
                $presentToday = StudentAttendance::whereDate('attendance_date', today())
                    ->where('organization_id', $organizationId)
                    ->whereHas('studentDetail', function ($q) use ($standard) {
                        $q->where('standard_id', $standard->id);
                    })
                    ->where('status', 1) // Present
                    ->count();

                $attendancePercentage = $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100) : 0;
                $distribution[$standard->name] = $attendancePercentage;
            }
        }

        return $distribution;
    }

    public function getRecentActivities()
    {
        return [
            [
                'type' => 'attendance_marked',
                'title' => 'Attendance Marked',
                'description' => 'Daily attendance marked for Class 5A',
                'time' => '2 hours ago',
                'icon' => 'green',
                'icon_class' => 'text-green-600'
            ],
            [
                'type' => 'attendance_updated',
                'title' => 'Attendance Updated',
                'description' => 'Updated attendance for Class 8B',
                'time' => '4 hours ago',
                'icon' => 'blue',
                'icon_class' => 'text-blue-600'
            ],
            [
                'type' => 'report_generated',
                'title' => 'Report Generated',
                'description' => 'Monthly attendance report generated',
                'time' => '1 day ago',
                'icon' => 'purple',
                'icon_class' => 'text-purple-600'
            ]
        ];
    }

    // Tab Navigation
    public function showTab($tab)
    {
        $this->activeTab = $tab;
        $this->subTab = array_key_first($this->tabs[$tab]);
        $this->resetPage();
    }

    public function setSubTab($subTab)
    {
        $this->subTab = $subTab;
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset([
            'teacher_detail_id',
            'standard_id',
            'section_id',
            'editId',
            'filteredSections'
        ]);
    }

    public function render()
    {
        $teacherAttendanceData = [];
        $teacherAttendanceList = [];
        $studentAttendanceList = [];

        // Load data based on active tab and sub tab
        if ($this->activeTab === 'teacher_attendance') {
            if ($this->subTab === 'teacher_attendance') {
                $teacherAttendanceData = TeacherDetail::with(['user', 'attendance' => function ($q) {
                    $q->whereDate('attendance_date', $this->teacher_attendance_date);
                }])->where('organization_id', Auth::user()->organization_id)->get();
            } elseif ($this->subTab === 'teacher_attendance_list') {
                // Fix the relationship here
                $teacherAttendanceList = TeacherAttendance::with(['teacherDetail.user', 'recordedBy'])
                    ->whereDate('attendance_date', $this->filter_date)
                    ->when($this->filter_teacher, function ($q) {
                        $q->where('teacher_detail_id', $this->filter_teacher);
                    })
                    ->when($this->filter_status, function ($q) {
                        $q->where('status', $this->filter_status);
                    })
                    ->latest()
                    ->paginate(20);
            }
        } elseif ($this->activeTab === 'student_attendance') {
            if ($this->subTab === 'student_attendance_list') {
                $studentAttendanceList = StudentAttendance::with(['studentDetail.user', 'studentDetail.standard', 'studentDetail.section', 'markerdBy'])
                    ->whereDate('attendance_date', $this->filter_date)
                    ->when($this->selected_standard, function ($q) {
                        $q->whereHas('studentDetail', function ($q2) {
                            $q2->where('standard_id', $this->selected_standard);
                        });
                    })
                    ->when($this->filter_status, function ($q) {
                        $q->where('status', $this->filter_status);
                    })
                    ->latest()
                    ->paginate(20);
            }
        }

        // Dashboard data
        $teacherStats = $this->teacherStats;
        $studentStats = $this->studentStats;
        $teacherTrendData = $this->getTeacherAttendanceTrendData();
        $teacherStatusData = $this->getTeacherStatusDistributionData();
        $studentTrendData = $this->getStudentAttendanceTrendData();
        $studentStatusData = $this->getStudentStatusDistributionData();
        $classWiseData = $this->getClassWiseAttendanceData();
        $recentActivities = $this->getRecentActivities();

        return view('livewire.admin.attendance', compact(
            'teacherAttendanceData',
            'teacherAttendanceList',
            'studentAttendanceList',
            'teacherStats',
            'studentStats',
            'teacherTrendData',
            'teacherStatusData',
            'studentTrendData',
            'studentStatusData',
            'classWiseData',
            'recentActivities'
        ));
    }
}
