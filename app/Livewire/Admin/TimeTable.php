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

    public string $createStandardId = '';
    public string $createSectionId  = '';
    public array  $createSections   = [];
    public array  $scheduleRows     = []; // one row per section_subject

    // ─── Delete confirm ──────────────────────────────────────────────────
    public bool   $showDeleteConfirm = false;
    public string $deleteStandardId  = '';
    public string $deleteSectionId   = '';

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
        4 => 'Thu', 5 => 'Fri', 6 => 'Sat',
    ];
    public array $daysOfWeekFull = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
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
        if (!in_array($day, $this->defaultDays, true)) return;
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
        $this->open   = true;
    }

    public function closePanel(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['createStandardId', 'createSectionId', 'scheduleRows']);
        $this->createSections = [];
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
        $this->scheduleRows    = [];
    }

    public function updatedCreateSectionId(): void
    {
        $this->buildScheduleRowsFromSection();
        $this->prefillRowsFromExisting();
    }

    /** Prefills scheduleRows from existing teacher_time_tables entries (auto-switches to edit mode). */
    private function prefillRowsFromExisting(): void
    {
        if (!$this->createStandardId || !$this->createSectionId) return;

        $org  = Auth::user()->organization_id;
        $rows = TeacherTimeTable::with('subject:id,name')
            ->where('organization_id', $org)
            ->where('standard_id', $this->createStandardId)
            ->where('section_id',  $this->createSectionId)
            ->get();
        if ($rows->isEmpty()) return;

        $this->isEdit = true;

        $groups = $rows->groupBy(fn($r) => $r->subject_id . '|' . $r->start_time . '|' . $r->end_time);
        foreach ($groups as $group) {
            $first       = $group->first();
            $byTeacher   = $group->groupBy('teacher_detail_id');
            $primaryId   = $byTeacher->sortByDesc(fn($g) => $g->count())->keys()->first();
            $fallbackId  = $byTeacher->keys()->first(fn($id) => $id != $primaryId);
            $primaryDays = $byTeacher[$primaryId]->pluck('day_of_week')->map(fn($d) => (int) $d)->values()->all();

            $idx = array_search((int) $first->subject_id, array_column($this->scheduleRows, 'subject_id'), true);
            $rowData = [
                'subject_id'          => (int) $first->subject_id,
                'subject_name'        => $first->subject?->name ?? 'Subject',
                'primary_teacher_id'  => (int) $primaryId,
                'start_time'          => substr($first->start_time, 0, 5),
                'end_time'            => substr($first->end_time, 0, 5),
                'selected_days'       => $primaryDays,
                'fallback_teacher_id' => $fallbackId ? (int) $fallbackId : '',
            ];

            if ($idx === false) {
                $this->scheduleRows[] = $rowData;
            } else {
                $this->scheduleRows[$idx] = $rowData;
            }
        }
    }

    /** Pre-populates one row per subject mapped to the chosen section. */
    private function buildScheduleRowsFromSection(): void
    {
        $this->scheduleRows = [];
        if (!$this->createStandardId || !$this->createSectionId) return;

        $org = Auth::user()->organization_id;
        $subjects = SectionSubject::with('subject')
            ->where('organization_id', $org)
            ->where('standard_id', $this->createStandardId)
            ->where('section_id', $this->createSectionId)
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('id')
            ->values();

        // Fallback to all active org subjects if no section_subjects rows exist
        if ($subjects->isEmpty()) {
            $subjects = Subject::where('organization_id', $org)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        foreach ($subjects as $s) {
            $this->scheduleRows[] = [
                'subject_id'          => (int) $s->id,
                'subject_name'        => $s->name,
                'primary_teacher_id'  => '',
                'start_time'          => '09:00',
                'end_time'            => '10:00',
                'selected_days'       => $this->defaultDays,
                'fallback_teacher_id' => '',
            ];
        }
    }

    public function toggleRowDay(int $rowIndex, int $day): void
    {
        if (!isset($this->scheduleRows[$rowIndex])) return;
        if (!in_array($day, $this->defaultDays, true)) return;
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

    public function getRowFallbackDays(int $rowIndex): array
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row) return [];
        return array_values(array_diff($this->defaultDays, $row['selected_days'] ?? []));
    }

    // ─── Conflict checks ─────────────────────────────────────────────────
    public function checkTeacherConflict(int $rowIndex, ?int $teacherId = null, ?array $days = null): ?string
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row) return null;
        $teacherId = $teacherId ?: (int) ($row['primary_teacher_id'] ?? 0);
        $days      = $days     ?? ($row['selected_days']                  ?? []);
        if (!$teacherId || !$row['start_time'] || !$row['end_time'] || empty($days)) return null;

        $busy = [];
        foreach ($days as $day) {
            $q = TeacherTimeTable::where('teacher_detail_id', $teacherId)
                ->where('day_of_week', $day)
                ->where('start_time', '<', $row['end_time'])
                ->where('end_time',   '>', $row['start_time']);

            if ($this->isEdit && $this->createStandardId && $this->createSectionId) {
                $q->where(function ($q2) {
                    $q2->where('standard_id', '!=', $this->createStandardId)
                       ->orWhere('section_id', '!=', $this->createSectionId);
                });
            }

            if ($q->exists()) $busy[] = $this->daysOfWeekFull[$day] ?? (string) $day;
        }
        return empty($busy) ? null : 'Teacher is busy on: ' . implode(', ', $busy);
    }

    public function checkSlotConflict(int $rowIndex): ?string
    {
        $row = $this->scheduleRows[$rowIndex] ?? null;
        if (!$row || !$this->createStandardId || !$this->createSectionId) return null;
        if (!$row['start_time'] || !$row['end_time']) return null;
        if (empty($row['selected_days'])) return null;

        $org = Auth::user()->organization_id;
        $taken = [];
        foreach ($row['selected_days'] as $day) {
            $q = TeacherTimeTable::where('organization_id', $org)
                ->where('standard_id', $this->createStandardId)
                ->where('section_id', $this->createSectionId)
                ->where('day_of_week', $day)
                ->where('start_time', '<', $row['end_time'])
                ->where('end_time',   '>', $row['start_time'])
                ->where('subject_id', '!=', $row['subject_id']);

            // Editing the whole section's timetable — entries are wiped & recreated on save,
            // so DB-side existing entries for this section don't block. Only conflicts within
            // the form between different subject rows are checked separately below.
            if ($this->isEdit) {
                $q->whereRaw('1=0');
            }

            if ($q->exists()) $taken[] = $this->daysOfWeekFull[$day] ?? (string) $day;
        }

        // Also check against sibling rows in the same form
        foreach ($this->scheduleRows as $j => $other) {
            if ($j === $rowIndex) continue;
            if (empty($other['start_time']) || empty($other['end_time'])) continue;
            if ($other['start_time'] >= $row['end_time'] || $other['end_time'] <= $row['start_time']) continue;
            $clash = array_intersect($other['selected_days'] ?? [], $row['selected_days']);
            if (!empty($clash)) {
                foreach ($clash as $day) {
                    $taken[] = ($this->daysOfWeekFull[$day] ?? (string) $day) . ' (with ' . ($other['subject_name'] ?? 'another subject') . ')';
                }
            }
        }

        return empty($taken) ? null : 'Time slot collision on: ' . implode(', ', array_unique($taken));
    }

    // ─── Save (create or edit) ───────────────────────────────────────────
    public function onSaveTimetable(): void
    {
        if (!$this->createStandardId) { $this->notification()->error('Please select a class.'); return; }
        if (!$this->createSectionId)  { $this->notification()->error('Please select a section.'); return; }

        // Filter out rows where no teacher is chosen — those subjects are simply not scheduled.
        $rowsToSave = collect($this->scheduleRows)
            ->filter(fn($r) => !empty($r['primary_teacher_id']))
            ->values()
            ->all();

        if (empty($rowsToSave) && !$this->isEdit) {
            $this->notification()->error('Assign at least one teacher to save the timetable.');
            return;
        }

        foreach ($rowsToSave as $i => $row) {
            $n = $row['subject_name'] ?? ('Subject ' . ($i + 1));
            if (empty($row['selected_days'])) {
                $this->notification()->error("{$n}: select at least one day."); return;
            }
            if (!$row['start_time'] || !$row['end_time'] || $row['start_time'] >= $row['end_time']) {
                $this->notification()->error("{$n}: invalid time range."); return;
            }
            $fallbackDays = array_values(array_diff($this->defaultDays, $row['selected_days']));
            if (!empty($fallbackDays) && empty($row['fallback_teacher_id'])) {
                $names = array_map(fn($d) => $this->daysOfWeekFull[$d] ?? $d, $fallbackDays);
                $this->notification()->error("{$n}: choose a fallback teacher for " . implode(', ', $names) . '.');
                return;
            }
            if (!empty($row['fallback_teacher_id']) && (int) $row['fallback_teacher_id'] === (int) $row['primary_teacher_id']) {
                $this->notification()->error("{$n}: fallback teacher must differ from primary teacher."); return;
            }
            $rowIndexInScheduleRows = array_search($row, $this->scheduleRows, true);
            if ($rowIndexInScheduleRows === false) $rowIndexInScheduleRows = $i;
            if ($conflict = $this->checkSlotConflict((int) $rowIndexInScheduleRows)) {
                $this->notification()->error("{$n}: {$conflict}"); return;
            }
            if ($conflict = $this->checkTeacherConflict((int) $rowIndexInScheduleRows)) {
                $this->notification()->error("{$n}: {$conflict}"); return;
            }
            if (!empty($row['fallback_teacher_id'])) {
                $conflict = $this->checkTeacherConflict((int) $rowIndexInScheduleRows, (int) $row['fallback_teacher_id'], $fallbackDays);
                if ($conflict) { $this->notification()->error("{$n} fallback: {$conflict}"); return; }
            }
        }

        try {
            DB::beginTransaction();
            $org = Auth::user()->organization_id;

            // Edit mode → wipe all existing entries for this (class, section) and recreate
            if ($this->isEdit) {
                TeacherTimeTable::where('organization_id', $org)
                    ->where('standard_id', $this->createStandardId)
                    ->where('section_id', $this->createSectionId)
                    ->delete();
            }

            $created = 0;
            foreach ($rowsToSave as $row) {
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

    // ─── Edit whole section's timetable ──────────────────────────────────
    public function onEditSection(int $standardId, int $sectionId): void
    {
        $this->resetForm();
        $this->createStandardId = (string) $standardId;
        $this->updatedCreateStandardId();
        $this->createSectionId  = (string) $sectionId;
        $this->buildScheduleRowsFromSection();
        $this->prefillRowsFromExisting();

        if (!$this->isEdit) {
            $this->notification()->error('No schedule found for this section.');
            return;
        }
        $this->open = true;
    }

    // ─── Delete whole section's timetable ────────────────────────────────
    public function onDeleteSection(int $standardId, int $sectionId): void
    {
        $this->deleteStandardId  = (string) $standardId;
        $this->deleteSectionId   = (string) $sectionId;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteStandardId  = '';
        $this->deleteSectionId   = '';
    }

    public function confirmDelete(): void
    {
        if (!$this->deleteStandardId || !$this->deleteSectionId) return;
        try {
            $org = Auth::user()->organization_id;
            TeacherTimeTable::where('organization_id', $org)
                ->where('standard_id', $this->deleteStandardId)
                ->where('section_id',  $this->deleteSectionId)
                ->delete();
            $this->notification()->success('Deleted!', 'Section timetable removed.');
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

        // CLASS VIEW: requires both class & section. TEACHER VIEW: requires teacher.
        $entries = collect();
        if ($this->viewMode === 'class' && $this->filterClass && $this->filterSection) {
            $entries = TeacherTimeTable::with([
                'teacher.user:id,name',
                'standard:id,name',
                'section:id,name',
                'subject:id,name,code',
            ])
                ->where('organization_id', $org)
                ->where('standard_id', $this->filterClass)
                ->where('section_id',  $this->filterSection)
                ->when(!empty($this->filterDays), fn($q) => $q->whereIn('day_of_week', $this->filterDays))
                ->get();
        } elseif ($this->viewMode === 'teacher' && $this->filterTeacher) {
            $entries = TeacherTimeTable::with([
                'teacher.user:id,name',
                'standard:id,name',
                'section:id,name',
                'subject:id,name,code',
            ])
                ->where('organization_id', $org)
                ->where('teacher_detail_id', $this->filterTeacher)
                ->when(!empty($this->filterDays), fn($q) => $q->whereIn('day_of_week', $this->filterDays))
                ->get();
        }

        // CLASS VIEW: one card containing all subject groups
        // TEACHER VIEW: one card per (class, section) of that teacher with the teacher's subject groups
        $sectionCards = collect();
        if ($entries->isNotEmpty()) {
            $sectionCards = $entries
                ->groupBy(fn($e) => $e->standard_id . '|' . ($e->section_id ?? ''))
                ->map(function ($items) {
                    $first = $items->first();
                    $subjectGroups = $items
                        ->groupBy(fn($e) => $e->subject_id . '|' . $e->start_time . '|' . $e->end_time)
                        ->map(function ($g) {
                            $byTeacher = $g->groupBy('teacher_detail_id')->map(function ($items) {
                                $first = $items->first();
                                return [
                                    'teacher_name' => $first->teacher?->user?->name ?? '—',
                                    'days'         => $items->pluck('day_of_week')->map(fn($d) => (int) $d)->sort()->values()->all(),
                                ];
                            })->sortByDesc(fn($t) => count($t['days']))->values()->all();

                            $first = $g->first();
                            return [
                                'subject'    => $first->subject?->name ?? '—',
                                'start_time' => $first->start_time,
                                'end_time'   => $first->end_time,
                                'teachers'   => $byTeacher,
                                'days'       => $g->pluck('day_of_week')->map(fn($d) => (int) $d)->unique()->sort()->values()->all(),
                            ];
                        })
                        ->sortBy('start_time')
                        ->values();

                    return [
                        'standard_id'    => $first->standard_id,
                        'section_id'     => $first->section_id,
                        'standard'       => $first->standard?->name ?? '—',
                        'section'        => $first->section?->name ?? '—',
                        'subject_groups' => $subjectGroups,
                    ];
                })
                ->sortBy([['standard', 'asc'], ['section', 'asc']])
                ->values();
        }

        return view('livewire.admin.time-table', [
            'sectionCards' => $sectionCards,
        ]);
    }
}
