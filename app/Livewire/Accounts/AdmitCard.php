<?php

namespace App\Livewire\Accounts;

use App\Models\Student\AdmitCard as ModelAdmitCard;
use App\Models\Admin\Exam;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AdmitCard extends Component
{
    use WithPagination;

    // ─── Filters ────────────────────────────────────────────────────────────────
    public $search         = '';
    public $examFilter     = '';
    public $standardFilter = '';
    public $sectionFilter  = '';
    public $statusFilter   = '';
    public int $perPage    = 15;

    // ─── Bulk Generate ───────────────────────────────────────────────────────────
    public bool $showBulkModal   = false;
    public $selectedExam         = '';
    public $selectedStandard     = '';
    public $selectedSection      = '';
    public array $selectedStudents = [];
    public bool $checkAttendance  = false;
    public bool $checkFeeClearance = false;
    public string $bulkInstructions = '';
    public string $bulkReportingTime = '08:30';

    // ─── View ───────────────────────────────────────────────────────────────────
    public bool $showViewModal = false;
    public ?int $viewCardId    = null;
    public $viewAdmitCard      = null;

    // ─── Delete ─────────────────────────────────────────────────────────────────
    public ?int $pendingDeleteId = null;

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    // ─── Filter Watchers ────────────────────────────────────────────────────────

    public function updatedSearch(): void          { $this->resetPage(); }
    public function updatedExamFilter(): void      { $this->resetPage(); }
    public function updatedStandardFilter(): void  { $this->sectionFilter = ''; $this->resetPage(); }
    public function updatedSectionFilter(): void   { $this->resetPage(); }
    public function updatedStatusFilter(): void    { $this->resetPage(); }

    public function updatedSelectedStandard(): void
    {
        $this->selectedSection  = '';
        $this->selectedStudents = [];
    }

    public function updatedSelectedSection(): void
    {
        $this->selectedStudents = [];
    }

    public function updatedSelectedExam(): void
    {
        $this->selectedStudents = [];
    }

    // ─── Bulk Generate ───────────────────────────────────────────────────────────

    public function openBulkModal(): void
    {
        $this->showBulkModal     = true;
        $this->selectedStudents  = [];
        $this->selectedExam      = '';
        $this->selectedStandard  = '';
        $this->selectedSection   = '';
        $this->checkAttendance   = false;
        $this->checkFeeClearance = false;
        $this->bulkInstructions  = '';
        $this->bulkReportingTime = '08:30';
    }

    public function closeBulkModal(): void
    {
        $this->showBulkModal = false;
    }

    public function toggleStudent(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_values(array_diff($this->selectedStudents, [$studentId]));
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function selectAllStudents(): void
    {
        $students = $this->getAvailableStudents();
        $this->selectedStudents = $students->pluck('id')->toArray();
    }

    public function deselectAllStudents(): void
    {
        $this->selectedStudents = [];
    }

    private function getAvailableStudents()
    {
        if (!$this->selectedExam || !$this->selectedStandard) {
            return collect();
        }

        return StudentDetail::with(['standard', 'section'])
            ->where('organization_id', $this->orgId())
            ->where('standard_id', $this->selectedStandard)
            ->when($this->selectedSection, fn($q) => $q->where('section_id', $this->selectedSection))
            ->whereDoesntHave('admitCards', fn($q) => $q->where('exam_id', $this->selectedExam))
            ->orderBy('full_name')
            ->get();
    }

    public function bulkGenerateAdmitCards(): void
    {
        $this->validate([
            'selectedExam'     => 'required|exists:exams,id',
            'selectedStandard' => 'required|exists:standards,id',
            'selectedStudents' => 'required|array|min:1',
        ]);

        $orgId    = $this->orgId();
        $exam     = Exam::find($this->selectedExam);
        $students = StudentDetail::with(['standard', 'section'])
            ->whereIn('id', $this->selectedStudents)->get();

        $generated = 0;
        foreach ($students as $student) {
            $existing = ModelAdmitCard::where('student_detail_id', $student->id)
                ->where('exam_id', $this->selectedExam)->first();
            if ($existing) continue;

            $admitCardNumber = ModelAdmitCard::generateAdmitCardNumber($orgId, $this->selectedExam);

            ModelAdmitCard::create([
                'student_detail_id'  => $student->id,
                'exam_id'            => $this->selectedExam,
                'organization_id'    => $orgId,
                'admit_card_number'  => $admitCardNumber,
                'student_name'       => $student->full_name,
                'father_name'        => $student->father_name,
                'mother_name'        => $student->mother_name,
                'roll_number'        => $student->roll_no ?? 'N/A',
                'standard_id'        => $student->standard_id,
                'section_id'         => $student->section_id,
                'exam_name'          => $exam->exam_name ?? '',
                'academic_year'      => $exam->academic_year ?? '',
                'reporting_time'     => $this->bulkReportingTime,
                'instructions'       => $this->bulkInstructions,
                'exam_center'        => '',
                'exam_center_address'=> '',
                'allowed_items'      => [],
                'prohibited_items'   => [],
                'subjects'           => [],
                'status'             => 'active',
                'issue_date'         => now(),
                'created_by'         => Auth::id(),
            ]);

            $generated++;
        }

        $this->closeBulkModal();
        session()->flash('success', "Successfully generated {$generated} admit card(s)!");
        $this->resetPage();
    }

    // ─── View ───────────────────────────────────────────────────────────────────

    public function viewCard(int $id): void
    {
        $this->viewAdmitCard = ModelAdmitCard::with([
            'studentDetail.standard',
            'studentDetail.section',
            'exam',
        ])->where('id', $id)->where('organization_id', $this->orgId())->first();

        if ($this->viewAdmitCard) {
            $this->showViewModal = true;
        }
    }

    public function closeViewModal(): void
    {
        $this->showViewModal  = false;
        $this->viewAdmitCard  = null;
    }

    // ─── Delete ─────────────────────────────────────────────────────────────────

    public function deleteCard(int $id): void
    {
        $this->pendingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->pendingDeleteId = null;
    }

    public function doDelete(): void
    {
        ModelAdmitCard::where('id', $this->pendingDeleteId)
            ->where('organization_id', $this->orgId())
            ->delete();
        $this->pendingDeleteId = null;
        session()->flash('success', 'Admit card deleted successfully!');
    }

    // ─── Reset Filters ───────────────────────────────────────────────────────────

    public function resetFilters(): void
    {
        $this->reset(['search', 'examFilter', 'standardFilter', 'sectionFilter', 'statusFilter']);
        $this->resetPage();
    }

    // ─── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        $orgId = $this->orgId();

        $standards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)->orderBy('order')->get();

        $exams = Exam::where('organization_id', $orgId)
            ->orderByDesc('created_at')->get();

        $filterSections = collect();
        if ($this->standardFilter) {
            $filterSections = Section::where('standard_id', $this->standardFilter)
                ->where('organization_id', $orgId)->get();
        }

        $bulkSections = collect();
        if ($this->selectedStandard) {
            $bulkSections = Section::where('standard_id', $this->selectedStandard)
                ->where('organization_id', $orgId)->get();
        }

        $availableStudents = $this->getAvailableStudents();

        $admitCards = ModelAdmitCard::with(['studentDetail.standard', 'studentDetail.section', 'exam'])
            ->where('organization_id', $orgId)
            ->when($this->search, fn($q) => $q->where(fn($s) =>
                $s->where('admit_card_number', 'like', "%{$this->search}%")
                  ->orWhere('student_name', 'like', "%{$this->search}%")
            ))
            ->when($this->examFilter, fn($q) => $q->where('exam_id', $this->examFilter))
            ->when($this->standardFilter, fn($q) => $q->where('standard_id', $this->standardFilter))
            ->when($this->sectionFilter, fn($q) => $q->where('section_id', $this->sectionFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate($this->perPage);

        // Analytics
        $totalIssued  = ModelAdmitCard::where('organization_id', $orgId)->count();
        $totalActive  = ModelAdmitCard::where('organization_id', $orgId)->where('status', 'active')->count();
        $totalStudents = StudentDetail::where('organization_id', $orgId)->count();

        return view('livewire.accounts.admit-card', compact(
            'standards', 'exams', 'filterSections', 'bulkSections',
            'availableStudents', 'admitCards',
            'totalIssued', 'totalActive', 'totalStudents'
        ));
    }
}
