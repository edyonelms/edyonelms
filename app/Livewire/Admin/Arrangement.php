<?php

namespace App\Livewire\Admin;

use App\Models\Admin\TeacherArrangement;
use App\Models\Admin\TeacherTimeTable;
use App\Models\Teacher\TeacherAttendance;
use App\Models\Teacher\TeacherDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Arrangement extends Component
{
    use WireUiActions, WithPagination;

    // ─── Date ────────────────────────────────────────────────────────────
    public $date;

    // ─── Create form ─────────────────────────────────────────────────────
    public $open               = false;
    public $selectedTeacherId  = '';
    public $absentTeachers     = [];
    public $teacherSlots       = [];   // timetable slots of selected absent teacher
    public $slotSubstitutes    = [];   // keyed by slot index: chosen substitute_id
    public $slotReasons        = [];   // keyed by slot index: reason text

    // ─── Stats ───────────────────────────────────────────────────────────
    public $totalTeachers    = 0;
    public $absentCount      = 0;
    public $availableCount   = 0;
    public $arrangementCount = 0;

    // ─── View filters ────────────────────────────────────────────────────
    public string $filterClass   = '';
    public string $filterTeacher = '';
    public $standards  = [];
    public $allTeachers = [];
    public int $perPage = 15;

    public function mount(): void
    {
        $this->date = Carbon::today()->format('Y-m-d');
        $this->loadPageData();
    }

    private function loadPageData(): void
    {
        $org       = Auth::user()->organization_id;
        $dayOfWeek = Carbon::parse($this->date)->dayOfWeekIso;

        // All teachers for filter dropdown
        $this->allTeachers = TeacherDetail::with('user')
            ->where('organization_id', $org)
            ->whereHas('user', fn($q) => $q->where('is_active', 1))
            ->get();

        // Standards for filter
        $this->standards = \App\Models\Student\Standard::where('organization_id', $org)->get();

        // Absent teachers for this date
        $absentDetailIds = TeacherAttendance::where('organization_id', $org)
            ->whereDate('attendance_date', $this->date)
            ->where('status', 0) // absent
            ->pluck('teacher_detail_id')
            ->toArray();

        $this->absentTeachers = TeacherDetail::with('user')
            ->whereIn('id', $absentDetailIds)
            ->get();

        // Stats
        $this->totalTeachers  = $this->allTeachers->count();
        $this->absentCount    = count($absentDetailIds);
        $this->availableCount = $this->totalTeachers - $this->absentCount;
        $this->arrangementCount = TeacherArrangement::where('organization_id', $org)
            ->whereDate('date', $this->date)->count();
    }

    public function updatedDate(): void
    {
        $this->resetCreateForm();
        $this->loadPageData();
        $this->resetPage();
    }

    // ─── Modal ───────────────────────────────────────────────────────────
    public function onCreateArrangement(): void
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
        $this->reset(['selectedTeacherId', 'teacherSlots', 'slotSubstitutes', 'slotReasons']);
    }

    // ─── When absent teacher selected, load their timetable slots ────────
    public function updatedSelectedTeacherId(): void
    {
        $this->teacherSlots     = [];
        $this->slotSubstitutes  = [];
        $this->slotReasons      = [];

        if (!$this->selectedTeacherId) return;

        $dayOfWeek = Carbon::parse($this->date)->dayOfWeekIso;
        $org       = Auth::user()->organization_id;

        // Get timetable slots for this teacher on this day
        $slots = TeacherTimeTable::with(['standard', 'section', 'subject'])
            ->where('teacher_detail_id', $this->selectedTeacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('organization_id', $org)
            ->orderBy('start_time')
            ->get();

        // Filter out slots that already have arrangements for this date
        $alreadyArrangedTimetableIds = TeacherArrangement::where('organization_id', $org)
            ->whereDate('date', $this->date)
            ->where('original_teacher_id', $this->selectedTeacherId)
            ->pluck('teacher_time_table_id')
            ->toArray();

        $this->teacherSlots = $slots->filter(function ($slot) use ($alreadyArrangedTimetableIds) {
            return !in_array($slot->id, $alreadyArrangedTimetableIds);
        })->values()->toArray();

        // Initialize substitute and reason for each slot
        foreach ($this->teacherSlots as $i => $slot) {
            $this->slotSubstitutes[$i] = '';
            $this->slotReasons[$i]     = '';
        }
    }

    // ─── Get available substitutes for a specific slot ───────────────────
    public function getAvailableSubstitutesForSlot($slotIndex): array
    {
        $slot = $this->teacherSlots[$slotIndex] ?? null;
        if (!$slot) return [];

        $org       = Auth::user()->organization_id;
        $dayOfWeek = Carbon::parse($this->date)->dayOfWeekIso;
        $startTime = $slot['start_time'];
        $endTime   = $slot['end_time'];

        // Get absent teacher detail IDs for this date
        $absentDetailIds = TeacherAttendance::where('organization_id', $org)
            ->whereDate('attendance_date', $this->date)
            ->where('status', 0)
            ->pluck('teacher_detail_id')
            ->toArray();

        // Teachers who have overlapping timetable on this day/time
        $busyTeacherIds = TeacherTimeTable::where('organization_id', $org)
            ->where('day_of_week', $dayOfWeek)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->pluck('teacher_detail_id')
            ->toArray();

        // Teachers who already have an arrangement as substitute for this time
        $alreadySubstitutingIds = TeacherArrangement::where('organization_id', $org)
            ->whereDate('date', $this->date)
            ->whereHas('timetable', function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->pluck('substitute_teacher_id')
            ->toArray();

        $excludeIds = array_unique(array_merge(
            $absentDetailIds,
            $busyTeacherIds,
            $alreadySubstitutingIds,
            [$this->selectedTeacherId]
        ));

        return TeacherDetail::with('user')
            ->where('organization_id', $org)
            ->whereHas('user', fn($q) => $q->where('is_active', 1))
            ->whereNotIn('id', $excludeIds)
            ->get()
            ->toArray();
    }

    // ─── Save arrangements ───────────────────────────────────────────────
    public function onSaveArrangements(): void
    {
        if (!$this->selectedTeacherId) {
            $this->notification()->error('Please select an absent teacher.');
            return;
        }

        $org     = Auth::user()->organization_id;
        $created = 0;
        $errors  = [];

        foreach ($this->teacherSlots as $i => $slot) {
            $substituteId = $this->slotSubstitutes[$i] ?? '';
            $reason       = $this->slotReasons[$i] ?? '';

            // Skip slots without substitute selected
            if (!$substituteId) continue;

            if (!$reason) {
                $errors[] = 'Slot ' . ($i + 1) . ': Please enter a reason.';
                continue;
            }

            // Check if arrangement already exists
            $exists = TeacherArrangement::where('organization_id', $org)
                ->whereDate('date', $this->date)
                ->where('teacher_time_table_id', $slot['id'])
                ->exists();

            if ($exists) {
                $errors[] = 'Slot ' . ($i + 1) . ': Arrangement already exists.';
                continue;
            }

            try {
                TeacherArrangement::create([
                    'original_teacher_id'    => $this->selectedTeacherId,
                    'substitute_teacher_id'  => $substituteId,
                    'teacher_time_table_id'  => $slot['id'],
                    'date'                   => $this->date,
                    'reason'                 => $reason,
                    'arranged_by'            => Auth::id(),
                    'organization_id'        => $org,
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = 'Slot ' . ($i + 1) . ': ' . $e->getMessage();
                logger()->error('Arrangement error: ' . $e->getMessage());
            }
        }

        if ($created > 0) {
            $msg = "Created {$created} arrangement(s).";
            if (!empty($errors)) {
                $msg .= ' Some slots had issues.';
            }
            $this->notification()->success('Success!', $msg);
            $this->closeModal();
            $this->loadPageData();
            $this->resetPage();
        } elseif (!empty($errors)) {
            $this->notification()->error('Error', implode(' ', $errors));
        } else {
            $this->notification()->warning('No arrangements', 'Please select substitute teachers for at least one slot.');
        }
    }

    // ─── Delete ──────────────────────────────────────────────────────────
    public function deleteArrangement($id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Are you sure?',
            'description' => 'This arrangement will be permanently deleted.',
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
            TeacherArrangement::findOrFail($id)->delete();
            $this->notification()->success('Deleted!', 'Arrangement deleted successfully.');
            $this->loadPageData();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->notification()->error('Error!', 'Failed to delete arrangement.');
        }
    }

    // ─── Render ──────────────────────────────────────────────────────────
    public function render()
    {
        $org = Auth::user()->organization_id;

        $arrangements = TeacherArrangement::with([
            'originalTeacher.user',
            'substituteTeacher.user',
            'timetable.standard',
            'timetable.section',
            'timetable.subject',
        ])
            ->where('organization_id', $org)
            ->whereDate('date', $this->date)
            ->when($this->filterClass, fn($q) => $q->whereHas(
                'timetable',
                fn($q) => $q->where('standard_id', $this->filterClass)
            ))
            ->when($this->filterTeacher, fn($q) => $q->where('original_teacher_id', $this->filterTeacher))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.arrangement', compact('arrangements'));
    }
}
