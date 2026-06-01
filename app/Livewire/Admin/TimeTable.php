<?php

namespace App\Livewire\Admin;

use App\Models\Admin\TeacherTimeTable;
use App\Models\Student\Section;
use App\Models\Student\SectionSubject;
use App\Models\Student\Standard;
use App\Models\Student\Subject;
use App\Models\Teacher\TeacherDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class TimeTable extends Component
{
    use WireUiActions, WithPagination;

    // ─── Tabs / view mode ────────────────────────────────────────────────
    public string $viewMode = 'class'; // 'class' | 'teacher'

    // ─── Filters ─────────────────────────────────────────────────────────
    public string $filterClass   = '';
    public string $filterSection = '';
    public string $filterTeacher = '';
    public array  $filterDays    = [];
    public array  $filterSections = [];
    public int    $perPage       = 20;

    // ─── Add / Edit panel state ──────────────────────────────────────────
    public bool $open    = false;
    public bool $isEdit  = false;
    /** group key of the row being edited: standard|section|subject|start|end */
    public string $editGroupKey = '';

    public string $createStandardId = '';
    public string $createSectionId  = '';
    public array  $createSections   = [];
    public array  $availableSubjects = [];
    public array  $scheduleRows     = [];
    public array  $usedSubjectIds   = [];

    // ─── View panel ──────────────────────────────────────────────────────
    public bool  $showView    = false;
    public array $viewData    = [];

    // ─── Delete confirm ──────────────────────────────────────────────────
    public bool   $showDeleteConfirm = false;
    public string $deleteTargetKey   = '';

    // ─── Lookup data ─────────────────────────────────────────────────────
    public $standards   = [];
    public $allTeachers = [];

    // ─── Stats ───────────────────────────────────────────────────────────
    public int $totalSchedules = 0;
    public int $totalTeachers  = 0;
    public int $totalClasses   = 0;
    public int $totalSubjects  = 0;

    public array $daysOfWeek = [
        1 => 'Mon', 2 => 'Tue', 3 => 'Wed',
        4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun',
    ];
    public array $daysOfWeekFull = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];
    /** Mon–Sat default for create */
    private array $defaultDays = [1, 2, 3, 4, 5, 6];

    protected $queryString = [
        'viewMode'      => ['except' => 'class'],
        'filterClass'   => ['except' => ''],
        'filterSection' => ['except' => ''],
        'filterTeacher' => ['except' => ''],
    ];

    public function mount(): void
    {
        $org = Auth::user()->organization_id;
        $this->standards   = Standard::where('organization_id', $org)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $this->allTeachers = TeacherDetail::with('user:id,name,email,is_active')
            ->where('organization_id', $org)
            ->whereHas('user', fn($q) => $q->where('is_active', 1))
            ->get();
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $org = Auth::user()->organization_id;
        $this->totalSchedules = TeacherTimeTable::where('organization_id', $org)->count();
        $this->totalTeachers  = $this->allTeachers->count();
        $this->totalClasses   = $this->standards->count();
        $this->totalSubjects  = Subject::where('organization_id', $org)
            ->where('is_active', true)
            ->count();
    }

    // ─── Tab switch ──────────────────────────────────────────────────────
    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['class', 'teacher'], true) ? $mode : 'class';
        $this->resetPage();
    }

    // ─── Filter handlers ─────────────────────────────────────────────────
    public function updatedFilterClass(): void
    {
        $this->filterSection  = '';
        $this->filterSections = $this->filterClass
            ? Section::where('standard_id', $this->filterClass)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->toArray()
            : [];
        $this->resetPage();
    }

    public function updatedFilterSection(): void { $this->resetPage(); }
    public function updatedFilterTeacher(): void { $this->resetPage(); }

    public function toggleFilterDay(int $day): void
    {
        $this->filterDays = in_array($day, $this->filterDays, true)
            ? array_values(array_diff($this->filterDays, [$day]))
            : array_merge($this->filterDays, [$day]);
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['filterClass', 'filterSection', 'filterTeacher', 'filterDays']);
        $this->filterSections = [];
        $this->resetPage();
    }

    // ─── Add / Edit panel ────────────────────────────────────────────────
    public function onCreateTimetable(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->editGroupKey = '';
        $this->open = true;
    }

    public function closePanel(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'createStandardId', 'createSectionId',
            'scheduleRows', 'usedSubjectIds',
        ]);
        $this->createSections    = [];
        $this->availableSubjects = [];
    }

    public function updatedCreateStandardId(): void
    {
        $this->createSections = $this->createStandardId
            ? Section::where('standard_id', $this->createStandardId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->toArray()
            : [];
        $this->createSectionId = '';
        $this->availableSubjects = [];
        $this->scheduleRows   = [];
        $this->usedSubjectIds = [];
    }

    public function updatedCreateSectionId(): void
    {
        $this->scheduleRows   = [];
        $this->usedSubjectIds = [];
        $this->loadAvailableSubjects();
    }

    private function loadAvailableSubjects(): void
    {
        if (!$this->createStandardId || !$this->createSectionId) {
            $this->availableSubjects = [];
            return;
        }

        $org = Auth::user()->organization_id;
        $subjects = SectionSubject::with('subject')
            ->where('organization_id', $org)
            ->where('standard_id', $this->createStandardId)
            ->where('section_id', $this->createSectionId)
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->values()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'code' => $s->code])
            ->toArray();

        if (empty($subjects)) {
            $subjects = Subject::where('organization_id', $org)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'code' => $s->code])
                ->toArray();
        }

        $this->availableSubjects = $subjects;
    }

    // ─── Subject rows ────────────────────────────────────────────────────
    public function addScheduleRow(): void
    {
        $this->scheduleRows[] = [
            'subject_id'          => '',
            'primary_teacher_id'  => '',
            'start_time'          => '09:00',
            'end_time'            => '10:00',
            'selected_days'       => $this->defaultDays,
            'fallback_teacher_id' => '',
        ];
    }

    public function removeScheduleRow(int $index): void
    {
        unset($this->scheduleRows[$index]);
        $this->scheduleRows = array_values($this->scheduleRows);
        $this->rebuildUsedSubjects();
    }

    private function rebuildUsedSubjects(): void
    {
        $this->usedSubjectIds = collect($this->scheduleRows)
            ->pluck('subject_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();
    }

    public function updatedScheduleRows($value, $key): void
    {
        if (str_contains($key, '.subject_id')) {
            $this->rebuildUsedSubjects();
        }
    }

    public function toggleRowDay(int $rowIndex, int $day): void
    {
        if (!isset($this->scheduleRows[$rowIndex])) return;
        $days = $this->scheduleRows[$rowIndex]['selected_days'] ?? [];
        $this->scheduleRows[$rowIndex]['selected_days'] = in_array($day, $days, true)
            ? array_values(array_diff($days, [$day]))
            : array_values(array_unique(array_merge($days, [$day])));
    }

    public function selectAllRowDays(int $rowIndex): void
    {
        if (!isset($this->scheduleRows[$rowIndex])) return;
        $this->scheduleRows[$rowIndex]['selected_days'] = $this->defaultDays;
    }

    // Days NOT selected, but within default Mon-Sat → those need a fallback teacher.
    public function getRowFallbackDays(int $rowIndex): array
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row) return [];
        $selected = $row['selected_days'] ?? [];
        return array_values(array_diff($this->defaultDays, $selected));
    }

    // ─── Subject pool for a row (avoid duplicate subject picks) ──────────
    public function getAvailableSubjectsForRow(int $rowIndex): array
    {
        $current = (int) ($this->scheduleRows[$rowIndex]['subject_id'] ?? 0);
        return array_values(array_filter(
            $this->availableSubjects,
            fn($s) => (int) $s['id'] === $current || !in_array((int) $s['id'], $this->usedSubjectIds, true)
        ));
    }

    // ─── Conflict checks ─────────────────────────────────────────────────
    /** Teacher already teaching elsewhere at the same time on a given day. */
    public function checkTeacherConflict(int $rowIndex, ?int $teacherId = null, ?array $days = null): ?string
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row) return null;
        $teacherId = $teacherId ?: ($row['primary_teacher_id'] ?? null);
        $days      = $days     ?? ($row['selected_days']      ?? []);
        if (!$teacherId || !$row['start_time'] || !$row['end_time'] || empty($days)) {
            return null;
        }

        $busy = [];
        foreach ($days as $day) {
            $q = TeacherTimeTable::where('teacher_detail_id', $teacherId)
                ->where('day_of_week', $day)
                ->where('start_time', '<', $row['end_time'])
                ->where('end_time',   '>', $row['start_time']);

            // exclude the entries belonging to the group being edited
            if ($this->isEdit && $this->editGroupKey) {
                [$std, $sec, $subj, $st, $en] = explode('|', $this->editGroupKey);
                $q->where(function ($q2) use ($std, $sec, $subj, $st, $en) {
                    $q2->where('standard_id', '!=', $std)
                       ->orWhere('section_id', '!=', $sec ?: null)
                       ->orWhere('subject_id', '!=', $subj)
                       ->orWhere('start_time', '!=', $st)
                       ->orWhere('end_time',   '!=', $en);
                });
            }

            if ($q->exists()) {
                $busy[] = $this->daysOfWeekFull[$day] ?? (string) $day;
            }
        }

        return empty($busy) ? null : 'Teacher is busy on: ' . implode(', ', $busy);
    }

    /** Same class+section already has someone in this slot on a chosen day. */
    public function checkSlotConflict(int $rowIndex): ?string
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row || !$this->createStandardId || !$row['start_time'] || !$row['end_time']) {
            return null;
        }
        if (empty($row['selected_days'])) return null;

        $org = Auth::user()->organization_id;
        $taken = [];
        foreach ($row['selected_days'] as $day) {
            $q = TeacherTimeTable::where('organization_id', $org)
                ->where('standard_id', $this->createStandardId)
                ->where('section_id', $this->createSectionId ?: null)
                ->where('day_of_week', $day)
                ->where('start_time', '<', $row['end_time'])
                ->where('end_time',   '>', $row['start_time']);

            // exclude this same subject's slot when editing or extending
            if (!empty($row['subject_id'])) {
                $q->where('subject_id', '!=', $row['subject_id']);
            }
            if ($this->isEdit && $this->editGroupKey) {
                [$std, $sec, $subj, $st, $en] = explode('|', $this->editGroupKey);
                $q->where(function ($q2) use ($std, $sec, $subj, $st, $en) {
                    $q2->where('standard_id', '!=', $std)
                       ->orWhere('section_id', '!=', $sec ?: null)
                       ->orWhere('subject_id', '!=', $subj)
                       ->orWhere('start_time', '!=', $st)
                       ->orWhere('end_time',   '!=', $en);
                });
            }

            if ($q->exists()) {
                $taken[] = $this->daysOfWeekFull[$day] ?? (string) $day;
            }
        }
        return empty($taken)
            ? null
            : 'This class already has a class scheduled in this time slot on: ' . implode(', ', $taken);
    }

    // ─── Save (create or edit) ───────────────────────────────────────────
    public function onSaveTimetable(): void
    {
        if (!$this->createStandardId) {
            $this->notification()->error('Please select a class.');
            return;
        }
        if (!$this->createSectionId) {
            $this->notification()->error('Please select a section.');
            return;
        }
        if (empty($this->scheduleRows)) {
            $this->notification()->error('Please add at least one subject.');
            return;
        }

        // Validate every row
        foreach ($this->scheduleRows as $i => $row) {
            $n = $i + 1;
            if (empty($row['subject_id']))         { $this->notification()->error("Row {$n}: select a subject."); return; }
            if (empty($row['primary_teacher_id'])) { $this->notification()->error("Row {$n}: select the primary teacher."); return; }
            if (empty($row['selected_days']))      { $this->notification()->error("Row {$n}: select at least one day."); return; }
            if (!$row['start_time'] || !$row['end_time'] || $row['start_time'] >= $row['end_time']) {
                $this->notification()->error("Row {$n}: invalid time range."); return;
            }

            // Fallback teacher required if some default days unchecked
            $fallbackDays = array_diff($this->defaultDays, $row['selected_days']);
            if (!empty($fallbackDays) && empty($row['fallback_teacher_id'])) {
                $names = array_map(fn($d) => $this->daysOfWeekFull[$d] ?? $d, $fallbackDays);
                $this->notification()->error("Row {$n}: choose a fallback teacher for " . implode(', ', $names) . '.');
                return;
            }
            if (!empty($row['fallback_teacher_id']) && $row['fallback_teacher_id'] === $row['primary_teacher_id']) {
                $this->notification()->error("Row {$n}: fallback teacher must differ from primary teacher.");
                return;
            }

            if ($conflict = $this->checkSlotConflict($i)) {
                $this->notification()->error("Row {$n}: {$conflict}"); return;
            }
            if ($conflict = $this->checkTeacherConflict($i)) {
                $this->notification()->error("Row {$n}: {$conflict}"); return;
            }
            if (!empty($row['fallback_teacher_id'])) {
                if ($conflict = $this->checkTeacherConflict($i, (int) $row['fallback_teacher_id'], array_values($fallbackDays))) {
                    $this->notification()->error("Row {$n} fallback: {$conflict}"); return;
                }
            }
        }

        try {
            DB::beginTransaction();
            $org = Auth::user()->organization_id;

            // For edit: delete the existing group rows first
            if ($this->isEdit && $this->editGroupKey) {
                [$std, $sec, $subj, $st, $en] = explode('|', $this->editGroupKey);
                TeacherTimeTable::where('organization_id', $org)
                    ->where('standard_id', $std)
                    ->where('section_id', $sec ?: null)
                    ->where('subject_id', $subj)
                    ->where('start_time', $st)
                    ->where('end_time', $en)
                    ->delete();
            }

            $created = 0;
            foreach ($this->scheduleRows as $row) {
                // primary teacher → selected days
                foreach ($row['selected_days'] as $day) {
                    TeacherTimeTable::create([
                        'organization_id'   => $org,
                        'assigned_by'       => Auth::id(),
                        'teacher_detail_id' => $row['primary_teacher_id'],
                        'standard_id'       => $this->createStandardId,
                        'section_id'        => $this->createSectionId,
                        'subject_id'        => $row['subject_id'],
                        'day_of_week'       => $day,
                        'start_time'        => $row['start_time'],
                        'end_time'          => $row['end_time'],
                        'is_active'         => true,
                    ]);
                    $created++;
                }
                // fallback teacher → remaining default days
                if (!empty($row['fallback_teacher_id'])) {
                    $fallbackDays = array_values(array_diff($this->defaultDays, $row['selected_days']));
                    foreach ($fallbackDays as $day) {
                        TeacherTimeTable::create([
                            'organization_id'   => $org,
                            'assigned_by'       => Auth::id(),
                            'teacher_detail_id' => $row['fallback_teacher_id'],
                            'standard_id'       => $this->createStandardId,
                            'section_id'        => $this->createSectionId,
                            'subject_id'        => $row['subject_id'],
                            'day_of_week'       => $day,
                            'start_time'        => $row['start_time'],
                            'end_time'          => $row['end_time'],
                            'is_active'         => true,
                        ]);
                        $created++;
                    }
                }
            }

            DB::commit();
            $this->notification()->success('Saved!', "{$created} timetable entries " . ($this->isEdit ? 'updated.' : 'created.'));
            $this->closePanel();
            $this->loadStats();
            $this->resetPage();
        } catch (\Throwable $e) {
            DB::rollBack();
            logger()->error('Timetable save error: ' . $e->getMessage());
            $this->notification()->error('Error!', $e->getMessage());
        }
    }

    // ─── View slide-in ───────────────────────────────────────────────────
    public function onViewGroup(string $groupKey): void
    {
        [$std, $sec, $subj, $st, $en] = explode('|', $groupKey);
        $org = Auth::user()->organization_id;
        $rows = TeacherTimeTable::with(['teacher.user:id,name', 'standard:id,name', 'section:id,name', 'subject:id,name,code'])
            ->where('organization_id', $org)
            ->where('standard_id', $std)
            ->where('section_id', $sec ?: null)
            ->where('subject_id', $subj)
            ->where('start_time', $st)
            ->where('end_time', $en)
            ->orderBy('day_of_week')
            ->get();

        if ($rows->isEmpty()) {
            $this->notification()->error('Schedule not found.');
            return;
        }

        $teachers = $rows->groupBy('teacher_detail_id')->map(function ($items) {
            return [
                'teacher_name' => $items->first()->teacher?->user?->name ?? '—',
                'days'         => $items->pluck('day_of_week')->map(fn($d) => $this->daysOfWeekFull[$d] ?? $d)->values()->all(),
            ];
        })->values()->all();

        $first = $rows->first();
        $this->viewData = [
            'group_key'  => $groupKey,
            'standard'   => $first->standard?->name,
            'section'    => $first->section?->name,
            'subject'    => $first->subject?->name,
            'start_time' => \Carbon\Carbon::parse($first->start_time)->format('h:i A'),
            'end_time'   => \Carbon\Carbon::parse($first->end_time)->format('h:i A'),
            'teachers'   => $teachers,
        ];
        $this->showView = true;
    }

    public function closeView(): void
    {
        $this->showView = false;
        $this->viewData = [];
    }

    public function onEditFromView(): void
    {
        $key = $this->viewData['group_key'] ?? '';
        $this->closeView();
        if ($key) $this->onEditGroup($key);
    }

    // ─── Edit slide-in ───────────────────────────────────────────────────
    public function onEditGroup(string $groupKey): void
    {
        [$std, $sec, $subj, $st, $en] = explode('|', $groupKey);
        $org = Auth::user()->organization_id;
        $rows = TeacherTimeTable::where('organization_id', $org)
            ->where('standard_id', $std)
            ->where('section_id', $sec ?: null)
            ->where('subject_id', $subj)
            ->where('start_time', $st)
            ->where('end_time', $en)
            ->get();

        if ($rows->isEmpty()) {
            $this->notification()->error('Schedule not found.');
            return;
        }

        $this->resetForm();
        $this->isEdit            = true;
        $this->editGroupKey      = $groupKey;
        $this->createStandardId  = (string) $std;
        $this->updatedCreateStandardId();
        $this->createSectionId   = (string) $sec;
        $this->loadAvailableSubjects();

        // Group by teacher to identify primary + fallback
        $byTeacher = $rows->groupBy('teacher_detail_id');
        $primaryTeacherId  = $byTeacher->sortByDesc(fn($g) => $g->count())->keys()->first();
        $fallbackTeacherId = $byTeacher->keys()->first(fn($id) => $id != $primaryTeacherId);

        $primaryDays = $byTeacher[$primaryTeacherId]->pluck('day_of_week')->map(fn($d) => (int) $d)->values()->all();

        $this->scheduleRows = [[
            'subject_id'          => (int) $subj,
            'primary_teacher_id'  => (int) $primaryTeacherId,
            'start_time'          => substr($st, 0, 5),
            'end_time'            => substr($en, 0, 5),
            'selected_days'       => $primaryDays,
            'fallback_teacher_id' => $fallbackTeacherId ? (int) $fallbackTeacherId : '',
        ]];
        $this->usedSubjectIds = [(int) $subj];
        $this->open = true;
    }

    // ─── Delete ──────────────────────────────────────────────────────────
    public function onDeleteGroup(string $groupKey): void
    {
        $this->deleteTargetKey   = $groupKey;
        $this->showDeleteConfirm = true;
    }
    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteTargetKey   = '';
    }
    public function confirmDelete(): void
    {
        if (!$this->deleteTargetKey) return;
        try {
            [$std, $sec, $subj, $st, $en] = explode('|', $this->deleteTargetKey);
            $org = Auth::user()->organization_id;
            TeacherTimeTable::where('organization_id', $org)
                ->where('standard_id', $std)
                ->where('section_id', $sec ?: null)
                ->where('subject_id', $subj)
                ->where('start_time', $st)
                ->where('end_time', $en)
                ->delete();
            $this->notification()->success('Deleted!', 'Schedule removed.');
            $this->loadStats();
        } catch (\Throwable $e) {
            $this->notification()->error('Error!', 'Failed to delete.');
        }
        $this->cancelDelete();
    }

    // ─── Render ──────────────────────────────────────────────────────────
    public function render()
    {
        $org = Auth::user()->organization_id;

        $query = TeacherTimeTable::with([
            'teacher.user:id,name',
            'standard:id,name',
            'section:id,name',
            'subject:id,name,code',
        ])
            ->where('organization_id', $org)
            ->when($this->filterClass,   fn($q) => $q->where('standard_id', $this->filterClass))
            ->when($this->filterSection, fn($q) => $q->where('section_id', $this->filterSection))
            ->when($this->filterTeacher, fn($q) => $q->where('teacher_detail_id', $this->filterTeacher))
            ->when(!empty($this->filterDays), fn($q) => $q->whereIn('day_of_week', $this->filterDays));

        if ($this->viewMode === 'teacher' && !$this->filterTeacher) {
            // teacher mode needs a teacher; fall back to empty until chosen
            $entries = collect();
        } else {
            $entries = $query->get();
        }

        // Group by class+section+subject+start+end and aggregate teachers/days
        $groups = $entries
            ->groupBy(fn($e) => implode('|', [
                $e->standard_id,
                $e->section_id ?? '',
                $e->subject_id,
                $e->start_time,
                $e->end_time,
            ]))
            ->map(function ($items, $key) {
                $first = $items->first();
                $byTeacher = $items->groupBy('teacher_detail_id')->map(function ($tItems) {
                    $teacher = $tItems->first()->teacher;
                    return [
                        'teacher_id'   => $tItems->first()->teacher_detail_id,
                        'teacher_name' => $teacher?->user?->name ?? '—',
                        'days'         => $tItems->pluck('day_of_week')->map(fn($d) => (int) $d)->sort()->values()->all(),
                    ];
                })->sortByDesc(fn($t) => count($t['days']))->values()->all();

                $allDays = $items->pluck('day_of_week')->map(fn($d) => (int) $d)->unique()->sort()->values()->all();

                return [
                    'key'         => $key,
                    'standard'    => $first->standard?->name,
                    'section'     => $first->section?->name,
                    'subject'     => $first->subject?->name,
                    'subject_id'  => $first->subject_id,
                    'start_time'  => $first->start_time,
                    'end_time'    => $first->end_time,
                    'teachers'    => $byTeacher,
                    'days'        => $allDays,
                ];
            })
            ->sortBy([['standard', 'asc'], ['section', 'asc'], ['start_time', 'asc']])
            ->values();

        // Manual pagination over grouped list (Livewire-aware)
        $page  = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $total = $groups->count();
        $items = $groups->slice(($page - 1) * $this->perPage, $this->perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items, $total, $this->perPage, $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('livewire.admin.time-table', [
            'groups'    => $paginator,
        ]);
    }
}
