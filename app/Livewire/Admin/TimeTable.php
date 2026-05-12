<?php

namespace App\Livewire\Admin;

use App\Models\Admin\TeacherTimeTable;
use App\Models\Teacher\TeacherDetail;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use App\Models\Student\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Collection;

class TimeTable extends Component
{
    use WireUiActions, WithPagination;

    // ─── Create Form ─────────────────────────────────────────────────────
    public $open = false;
    public $createStandardId  = '';
    public $createSectionId   = '';
    public $createSections    = [];
    public $availableSubjects = [];
    public $scheduleRows      = []; // Each row: {subject_id, teacher_id, start_time, end_time, selected_days}
    public $usedSubjectIds    = [];

    // ─── Filter / View ───────────────────────────────────────────────────
    public string $filterClass   = '';
    public string $filterSection = '';
    public string $filterTeacher = '';
    public $filterDays           = []; // chip-based multi-select
    public $filterSections       = [];
    public int    $perPage       = 15;

    // ─── Lookup data ─────────────────────────────────────────────────────
    public $standards = [];
    public $allTeachers = [];
    public $allSubjects = [];

    // ─── Stats ───────────────────────────────────────────────────────────
    public $totalSchedules = 0;
    public $totalTeachers  = 0;
    public $totalClasses   = 0;
    public $totalSubjects  = 0;

    public $daysOfWeek = [
        1 => 'Mon',
        2 => 'Tue',
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat',
        7 => 'Sun',
    ];

    public $daysOfWeekFull = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    protected $queryString = [
        'filterClass'   => ['except' => ''],
        'filterSection' => ['except' => ''],
        'filterTeacher' => ['except' => ''],
    ];

    public function mount(): void
    {
        $org = Auth::user()->organization_id;
        $this->standards   = Standard::where('organization_id', $org)->where('is_active', true)->get();
        $this->allTeachers = TeacherDetail::with('user')->where('organization_id', $org)
            ->whereHas('user', fn($q) => $q->where('is_active', 1))->get();
        $this->allSubjects = Subject::where('organization_id', $org)->get();
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $org = Auth::user()->organization_id;
        $this->totalSchedules = TeacherTimeTable::where('organization_id', $org)->count();
        $this->totalTeachers  = $this->allTeachers->count();
        $this->totalClasses   = $this->standards->count();
        $this->totalSubjects  = $this->allSubjects->count();
    }

    // ─── Filter handlers ─────────────────────────────────────────────────
    public function updatedFilterClass(): void
    {
        $this->resetPage();
        $this->filterSection  = '';
        $this->filterSections = $this->filterClass
            ? Section::where('standard_id', $this->filterClass)->get()
            : [];
    }

    public function updatedFilterSection(): void
    {
        $this->resetPage();
    }
    public function updatedFilterTeacher(): void
    {
        $this->resetPage();
    }

    public function toggleFilterDay($day): void
    {
        if (in_array($day, $this->filterDays)) {
            $this->filterDays = array_values(array_diff($this->filterDays, [$day]));
        } else {
            $this->filterDays[] = $day;
        }
        $this->resetPage();
    }

    public function clearFilterDays(): void
    {
        $this->filterDays = [];
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['filterClass', 'filterSection', 'filterTeacher', 'filterDays']);
        $this->filterSections = [];
        $this->resetPage();
    }

    // ─── Create Modal ────────────────────────────────────────────────────
    public function onCreateTimetable(): void
    {
        $this->resetCreateForm();
        $this->open = true;
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetCreateForm();
    }

    private function resetCreateForm(): void
    {
        $this->reset(['createStandardId', 'createSectionId', 'scheduleRows', 'usedSubjectIds']);
        $this->createSections    = [];
        $this->availableSubjects = [];
    }

    public function updatedCreateStandardId(): void
    {
        $this->createSections = $this->createStandardId
            ? Section::where('standard_id', $this->createStandardId)->where('is_active', true)->get()
            : [];
        $this->createSectionId = '';
        $this->scheduleRows    = [];
        $this->usedSubjectIds  = [];
        $this->loadAvailableSubjects();
    }

    public function updatedCreateSectionId(): void
    {
        $this->scheduleRows   = [];
        $this->usedSubjectIds = [];
        $this->loadAvailableSubjects();
    }

    private function loadAvailableSubjects(): void
    {
        if (!$this->createStandardId) {
            $this->availableSubjects = [];
            return;
        }

        // Get subjects linked to this standard, or all org subjects
        $standard = Standard::with('subjects')->find($this->createStandardId);
        if ($standard && $standard->subjects->isNotEmpty()) {
            $this->availableSubjects = $standard->subjects->toArray();
        } else {
            $this->availableSubjects = $this->allSubjects->toArray();
        }
    }

    // ─── Schedule rows (add subject rows without saving) ─────────────────
    public function addScheduleRow(): void
    {
        $this->scheduleRows[] = [
            'subject_id'    => '',
            'teacher_id'    => '',
            'start_time'    => '09:00',
            'end_time'      => '10:00',
            'selected_days' => [],
        ];
    }

    public function removeScheduleRow($index): void
    {
        $removedSubjectId = $this->scheduleRows[$index]['subject_id'] ?? null;
        unset($this->scheduleRows[$index]);
        $this->scheduleRows = array_values($this->scheduleRows);

        // Rebuild used subject IDs
        $this->usedSubjectIds = collect($this->scheduleRows)
            ->pluck('subject_id')
            ->filter()
            ->values()
            ->toArray();
    }

    public function updatedScheduleRows($value, $key): void
    {
        // When subject changes, rebuild used list
        if (str_contains($key, 'subject_id')) {
            $this->usedSubjectIds = collect($this->scheduleRows)
                ->pluck('subject_id')
                ->filter()
                ->values()
                ->toArray();
        }
    }

    public function toggleRowDay($rowIndex, $day): void
    {
        $days = $this->scheduleRows[$rowIndex]['selected_days'] ?? [];
        if (in_array($day, $days)) {
            $days = array_values(array_diff($days, [$day]));
        } else {
            $days[] = $day;
        }
        $this->scheduleRows[$rowIndex]['selected_days'] = $days;
    }

    public function selectAllRowDays($rowIndex): void
    {
        $this->scheduleRows[$rowIndex]['selected_days'] = array_keys($this->daysOfWeek);
    }

    // ─── Check teacher availability ──────────────────────────────────────
    public function checkTeacherConflict($rowIndex): ?string
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row || !$row['teacher_id'] || !$row['start_time'] || !$row['end_time'] || empty($row['selected_days'])) {
            return null;
        }

        $conflicts = [];
        foreach ($row['selected_days'] as $day) {
            $dayName = $this->daysOfWeekFull[$day] ?? $day;

            $exists = TeacherTimeTable::where('teacher_detail_id', $row['teacher_id'])
                ->where('day_of_week', $day)
                ->where(function ($q) use ($row) {
                    $q->where(function ($q2) use ($row) {
                        $q2->where('start_time', '<', $row['end_time'])
                            ->where('end_time', '>', $row['start_time']);
                    });
                })
                ->exists();

            if ($exists) {
                $conflicts[] = $dayName;
            }
        }

        return !empty($conflicts)
            ? 'Teacher is busy on: ' . implode(', ', $conflicts)
            : null;
    }

    // ─── Get remaining subjects for a row ────────────────────────────────
    public function getAvailableSubjectsForRow($rowIndex): array
    {
        $currentSubjectId = $this->scheduleRows[$rowIndex]['subject_id'] ?? null;

        return array_filter($this->availableSubjects, function ($subject) use ($currentSubjectId) {
            return $subject['id'] == $currentSubjectId || !in_array($subject['id'], $this->usedSubjectIds);
        });
    }

    // ─── Save all schedule rows at once ──────────────────────────────────
    public function onSaveTimetable(): void
    {
        if (!$this->createStandardId) {
            $this->notification()->error('Please select a class.');
            return;
        }

        if (empty($this->scheduleRows)) {
            $this->notification()->error('Please add at least one subject schedule.');
            return;
        }

        // Validate each row
        foreach ($this->scheduleRows as $i => $row) {
            if (empty($row['subject_id'])) {
                $this->notification()->error('Row ' . ($i + 1) . ': Please select a subject.');
                return;
            }
            if (empty($row['teacher_id'])) {
                $this->notification()->error('Row ' . ($i + 1) . ': Please select a teacher.');
                return;
            }
            if (empty($row['selected_days'])) {
                $this->notification()->error('Row ' . ($i + 1) . ': Please select at least one day.');
                return;
            }
            if (!$row['start_time'] || !$row['end_time'] || $row['start_time'] >= $row['end_time']) {
                $this->notification()->error('Row ' . ($i + 1) . ': Invalid time range.');
                return;
            }

            // Check conflicts
            $conflict = $this->checkTeacherConflict($i);
            if ($conflict) {
                $this->notification()->error('Row ' . ($i + 1) . ': ' . $conflict);
                return;
            }
        }

        try {
            DB::beginTransaction();

            $org       = Auth::user()->organization_id;
            $created   = 0;
            $skipped   = 0;

            foreach ($this->scheduleRows as $row) {
                foreach ($row['selected_days'] as $day) {
                    // Check duplicate
                    $exists = TeacherTimeTable::where('organization_id', $org)
                        ->where('standard_id', $this->createStandardId)
                        ->where('section_id', $this->createSectionId ?: null)
                        ->where('subject_id', $row['subject_id'])
                        ->where('day_of_week', $day)
                        ->where('start_time', $row['start_time'])
                        ->where('end_time', $row['end_time'])
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    TeacherTimeTable::create([
                        'organization_id'   => $org,
                        'assigned_by'       => Auth::id(),
                        'teacher_detail_id' => $row['teacher_id'],
                        'standard_id'       => $this->createStandardId,
                        'section_id'        => $this->createSectionId ?: null,
                        'subject_id'        => $row['subject_id'],
                        'day_of_week'       => $day,
                        'start_time'        => $row['start_time'],
                        'end_time'          => $row['end_time'],
                    ]);
                    $created++;
                }
            }

            DB::commit();

            $msg = "Created {$created} timetable entries.";
            if ($skipped > 0) {
                $msg .= " {$skipped} duplicates skipped.";
            }

            $this->notification()->success('Success!', $msg);
            $this->closeModal();
            $this->loadStats();
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error('Error!', $e->getMessage());
            logger()->error('Timetable save error: ' . $e->getMessage());
        }
    }

    // ─── Delete ──────────────────────────────────────────────────────────
    public function deleteEntry($id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Are you sure?',
            'description' => 'This timetable entry will be permanently deleted.',
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'performDelete',
                'params' => $id,
            ],
            'reject' => ['label' => 'Cancel'],
        ]);
    }

    public function performDelete($id): void
    {
        try {
            TeacherTimeTable::findOrFail($id)->delete();
            $this->notification()->success('Deleted!', 'Timetable entry deleted successfully.');
            $this->loadStats();
        } catch (\Exception $e) {
            $this->notification()->error('Error!', 'Failed to delete entry.');
        }
    }

    public function render()
    {
        $org = Auth::user()->organization_id;

        $timetable = TeacherTimeTable::with(['teacher.user', 'standard', 'section', 'subject'])
            ->where('organization_id', $org)
            ->when($this->filterClass, fn($q) => $q->where('standard_id', $this->filterClass))
            ->when($this->filterSection, fn($q) => $q->where('section_id', $this->filterSection))
            ->when($this->filterTeacher, fn($q) => $q->where('teacher_detail_id', $this->filterTeacher))
            ->when(!empty($this->filterDays), fn($q) => $q->whereIn('day_of_week', $this->filterDays))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate($this->perPage);

        return view('livewire.admin.time-table', compact('timetable'));
    }
}
