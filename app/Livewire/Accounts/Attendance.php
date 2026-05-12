<?php

namespace App\Livewire\Accounts;

use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentAttendance;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherAttendance;
use App\Models\Teacher\TeacherDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Attendance extends Component
{
    use WithPagination;

    // ─── Main Tabs ─────────────────────────────────────────────
    public string $activeTab = 'teacher'; // teacher | student
    public string $subTab    = 'by_date'; // by_date | by_teacher  (teacher tab)
                                           // by_date | by_student | by_class  (student tab)

    // ─── Teacher Attendance ────────────────────────────────────
    public string $teacherDate = '';
    public array  $teacherAttendanceData = [];  // [teacher_id => ['status'=>..., 'db_status'=>..., 'remarks'=>...]]

    // Teacher list view filters
    public string $filterTeacherDate    = '';
    public string $filterTeacherId      = '';
    public string $filterTeacherStatus  = '';

    // ─── Student Attendance ────────────────────────────────────
    public string $studentDate     = '';
    public string $selectedClass   = '';
    public string $selectedSection = '';

    // Student list view filters
    public string $filterStudentDate    = '';
    public string $filterStudentClass   = '';
    public string $filterStudentSection = '';
    public string $filterStudentStatus  = '';

    // Calendar view
    public string $calendarMonth = '';  // Y-m format e.g. 2026-04
    public string $calendarClass  = '';
    public string $calendarSection = '';
    public string $calendarStudentId = '';

    public int $perPage = 20;

    public function mount(): void
    {
        $this->teacherDate       = now()->format('Y-m-d');
        $this->studentDate       = now()->format('Y-m-d');
        $this->filterTeacherDate = now()->format('Y-m-d');
        $this->filterStudentDate = now()->format('Y-m-d');
        $this->calendarMonth     = now()->format('Y-m');

        $this->loadTeacherAttendanceData();
    }

    // ─── Tab Navigation ────────────────────────────────────────

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->subTab    = 'by_date';
        $this->resetPage();
    }

    public function setSubTab(string $subTab): void
    {
        $this->subTab = $subTab;
        $this->resetPage();
    }

    // ─── Teacher Attendance ────────────────────────────────────

    public function loadTeacherAttendanceData(): void
    {
        $orgId    = Auth::user()->organization_id;
        $teachers = TeacherDetail::with(['user', 'attendance' => function ($q) {
            $q->whereDate('attendance_date', $this->teacherDate)
              ->where('organization_id', Auth::user()->organization_id);
        }])->where('organization_id', $orgId)->get();

        $this->teacherAttendanceData = [];
        foreach ($teachers as $teacher) {
            $existing = $teacher->attendance->first();
            $dbStatus = $existing ? $existing->status : 1;
            $this->teacherAttendanceData[$teacher->id] = [
                'status'    => $this->getStatusLabel($dbStatus),
                'db_status' => $dbStatus,
                'remarks'   => $existing ? ($existing->remarks ?? '') : '',
            ];
        }
    }

    public function updatedTeacherDate(): void
    {
        $this->loadTeacherAttendanceData();
    }

    public function updateTeacherStatus(int $teacherId, string $status): void
    {
        $dbStatus = $this->getStatusValue($status);

        TeacherAttendance::updateOrCreate(
            [
                'teacher_detail_id' => $teacherId,
                'organization_id'   => Auth::user()->organization_id,
                'attendance_date'   => $this->teacherDate,
            ],
            [
                'status'    => $dbStatus,
                'remarks'   => $this->teacherAttendanceData[$teacherId]['remarks'] ?? '',
                'marked_by' => Auth::id(),
            ]
        );

        $this->teacherAttendanceData[$teacherId]['status']    = $status;
        $this->teacherAttendanceData[$teacherId]['db_status'] = $dbStatus;
    }

    public function saveAllTeacherAttendance(): void
    {
        $orgId    = Auth::user()->organization_id;
        $markedBy = Auth::id();

        DB::beginTransaction();
        try {
            foreach ($this->teacherAttendanceData as $teacherId => $data) {
                TeacherAttendance::updateOrCreate(
                    [
                        'teacher_detail_id' => $teacherId,
                        'organization_id'   => $orgId,
                        'attendance_date'   => $this->teacherDate,
                    ],
                    [
                        'status'    => $data['db_status'],
                        'remarks'   => $data['remarks'] ?? '',
                        'marked_by' => $markedBy,
                    ]
                );
            }
            DB::commit();
            session()->flash('success', 'Teacher attendance saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    // ─── Student Attendance ────────────────────────────────────

    public function updatedSelectedClass(): void
    {
        $this->selectedSection = '';
        $this->resetPage();
    }

    public function updatedFilterStudentClass(): void
    {
        $this->filterStudentSection = '';
        $this->resetPage();
    }

    public function updatedCalendarClass(): void
    {
        $this->calendarSection   = '';
        $this->calendarStudentId = '';
    }

    // ─── Statistics ────────────────────────────────────────────

    public function getTeacherStatsForDate(string $date): array
    {
        $orgId = Auth::user()->organization_id;
        $stats = TeacherAttendance::whereDate('attendance_date', $date)
            ->where('organization_id', $orgId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'total'    => TeacherDetail::where('organization_id', $orgId)->count(),
            'present'  => $stats[1] ?? 0,
            'absent'   => $stats[0] ?? 0,
            'late'     => $stats[2] ?? 0,
            'half_day' => $stats[3] ?? 0,
        ];
    }

    public function getStudentStatsForDate(string $date): array
    {
        $orgId = Auth::user()->organization_id;
        $stats = StudentAttendance::whereDate('attendance_date', $date)
            ->where('organization_id', $orgId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return [
            'total'   => StudentDetail::where('organization_id', $orgId)->count(),
            'present' => $stats[1] ?? 0,
            'absent'  => $stats[0] ?? 0,
            'late'    => $stats[2] ?? 0,
        ];
    }

    // ─── Calendar Data ─────────────────────────────────────────

    public function getCalendarData(): array
    {
        if (!$this->calendarStudentId && !$this->calendarClass) {
            return [];
        }

        $orgId  = Auth::user()->organization_id;
        $month  = Carbon::createFromFormat('Y-m', $this->calendarMonth);
        $start  = $month->copy()->startOfMonth();
        $end    = $month->copy()->endOfMonth();

        $query = StudentAttendance::whereBetween('attendance_date', [$start, $end])
            ->where('organization_id', $orgId);

        if ($this->calendarStudentId) {
            $query->where('student_detail_id', $this->calendarStudentId);
        } elseif ($this->calendarClass) {
            $query->whereHas('studentDetail', function ($q) {
                $q->where('standard_id', $this->calendarClass);
                if ($this->calendarSection) {
                    $q->where('section_id', $this->calendarSection);
                }
            });
        }

        return $query->get()
            ->groupBy(fn($r) => Carbon::parse($r->attendance_date)->format('Y-m-d'))
            ->map(fn($group) => [
                'present'  => $group->where('status', 1)->count(),
                'absent'   => $group->where('status', 0)->count(),
                'late'     => $group->where('status', 2)->count(),
                'half_day' => $group->where('status', 3)->count(),
            ])
            ->toArray();
    }

    // ─── Helpers ───────────────────────────────────────────────

    protected function getStatusValue(string $status): int
    {
        return ['present' => 1, 'absent' => 0, 'late' => 2, 'half_day' => 3, 'none' => 4][$status] ?? 1;
    }

    protected function getStatusLabel(int $dbStatus): string
    {
        return [1 => 'present', 0 => 'absent', 2 => 'late', 3 => 'half_day', 4 => 'none'][$dbStatus] ?? 'none';
    }

    public function render()
    {
        $orgId = Auth::user()->organization_id;

        // Always needed
        $standards = Standard::where('organization_id', $orgId)->orderBy('name')->get();
        $teachers  = TeacherDetail::with('user')
            ->where('organization_id', $orgId)
            ->orderBy('created_at')
            ->get();

        // Sections for student tab
        $sections = collect();
        $filterSections = collect();
        $calendarSections = collect();

        if ($this->selectedClass) {
            $sections = Section::where('standard_id', $this->selectedClass)->orderBy('name')->get();
        }
        if ($this->filterStudentClass) {
            $filterSections = Section::where('standard_id', $this->filterStudentClass)->orderBy('name')->get();
        }
        if ($this->calendarClass) {
            $calendarSections = Section::where('standard_id', $this->calendarClass)->orderBy('name')->get();
        }

        // Teacher attendance (mark mode)
        $teacherList = TeacherDetail::with(['user', 'attendance' => function ($q) {
            $q->whereDate('attendance_date', $this->teacherDate)
              ->where('organization_id', Auth::user()->organization_id);
        }])->where('organization_id', $orgId)->get();

        // Teacher attendance list (by_date sub-tab or by_teacher)
        $teacherAttendanceList = collect();
        if ($this->activeTab === 'teacher' && $this->subTab === 'by_teacher') {
            $query = TeacherAttendance::with(['teacherDetail.user'])
                ->whereDate('attendance_date', $this->filterTeacherDate)
                ->where('organization_id', $orgId);

            if ($this->filterTeacherId) {
                $query->where('teacher_detail_id', $this->filterTeacherId);
            }
            if ($this->filterTeacherStatus !== '') {
                $query->where('status', $this->getStatusValue($this->filterTeacherStatus));
            }
            $teacherAttendanceList = $query->latest()->paginate($this->perPage);
        }

        // Student attendance list
        $studentList      = collect();
        $studentAttendanceList = collect();
        $calendarData     = [];
        $calendarStudents = collect();

        if ($this->activeTab === 'student') {
            if ($this->subTab === 'by_date') {
                $query = StudentDetail::with(['standard', 'section', 'attendance' => function ($q) {
                    $q->whereDate('attendance_date', $this->studentDate)
                      ->where('organization_id', Auth::user()->organization_id);
                }])->where('organization_id', $orgId);

                if ($this->selectedClass) {
                    $query->where('standard_id', $this->selectedClass);
                }
                if ($this->selectedSection) {
                    $query->where('section_id', $this->selectedSection);
                }

                $studentList = $query->orderBy('full_name')->paginate($this->perPage);

            } elseif ($this->subTab === 'by_student') {
                $query = StudentAttendance::with(['studentDetail.standard', 'studentDetail.section'])
                    ->whereDate('attendance_date', $this->filterStudentDate)
                    ->where('organization_id', $orgId);

                if ($this->filterStudentClass) {
                    $query->whereHas('studentDetail', fn($q) =>
                        $q->where('standard_id', $this->filterStudentClass)
                    );
                }
                if ($this->filterStudentSection) {
                    $query->whereHas('studentDetail', fn($q) =>
                        $q->where('section_id', $this->filterStudentSection)
                    );
                }
                if ($this->filterStudentStatus !== '') {
                    $query->where('status', $this->getStatusValue($this->filterStudentStatus));
                }
                $studentAttendanceList = $query->latest()->paginate($this->perPage);

            } elseif ($this->subTab === 'by_class') {
                $calendarData = $this->getCalendarData();

                if ($this->calendarClass) {
                    $q = StudentDetail::where('organization_id', $orgId)
                        ->where('standard_id', $this->calendarClass);
                    if ($this->calendarSection) {
                        $q->where('section_id', $this->calendarSection);
                    }
                    $calendarStudents = $q->orderBy('full_name')->get();
                }
            }
        }

        $teacherStats = $this->getTeacherStatsForDate($this->teacherDate);
        $studentStats = $this->getStudentStatsForDate($this->studentDate);

        return view('livewire.accounts.attendance', compact(
            'standards', 'teachers', 'sections', 'filterSections', 'calendarSections',
            'teacherList', 'teacherAttendanceList',
            'studentList', 'studentAttendanceList',
            'calendarData', 'calendarStudents',
            'teacherStats', 'studentStats'
        ));
    }
}
