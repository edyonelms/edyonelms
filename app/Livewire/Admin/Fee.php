<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Admin\Fee\FeeSettings;
use App\Models\Admin\Fee\FeeStructure;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Fee extends Component
{
    use WireUiActions, WithPagination;

    public string $activeTab = 'fee_structure';

    // ─── Fee Structure ─────────────────────────────────────────────────────────
    public $structureStandardId = '';
    public $structureSectionId  = '';
    public $feeName             = '';
    public $feeAmount           = '';
    public $structureFeeType    = 'academic';
    public $academicYear        = '';
    public $editStructureId     = null;
    public $structureModalOpen  = false;

    // Filters for structure list
    public $filterStructureStandard  = '';
    public $filterStructureSection   = '';
    public $filterStructureYear      = '';

    // ─── Fee Submission ────────────────────────────────────────────────────────
    public $submissionStandardId = '';
    public $submissionSectionId  = '';
    public $selectedStudentId    = '';
    public $classStructures      = [];
    public $studentTransactions  = [];

    // Payment form
    public $submitAmount      = '';
    public $submitFeeType     = 'academic';
    public $submitPaymentMode = 'cash';
    public $submitDate        = '';
    public $submitRemark      = '';
    public $submittedBy       = '';

    // ─── View Fee ─────────────────────────────────────────────────────────────
    public string $viewSubTab          = 'by_student';
    public $viewStudentStandardId      = '';
    public $viewStudentSectionId       = '';
    public $viewStudentId              = '';
    public $studentFeeView             = [];

    public $viewClassStandardId        = '';
    public $viewClassSectionId         = '';
    public $classFeeList               = [];

    // ─── Analytics ────────────────────────────────────────────────────────────
    public $analyticsStandardId  = '';
    public $analyticsSectionId   = '';
    public $analyticsData        = [];
    public $analyticsStudentList = [];

    // ─── Payments ─────────────────────────────────────────────────────────────
    public $paymentModeFilter    = '';
    public $paymentStandardId    = '';
    public $paymentSectionId     = '';
    public $paymentPeriodStats   = [];

    // ─── Penalties ────────────────────────────────────────────────────────────
    public $penaltyPerDay    = '0';
    public $cycleType        = 'monthly';
    public $dueDayOfMonth    = '10';
    public $penaltyAnalytics = [];

    // ─── Shared ───────────────────────────────────────────────────────────────
    public $search      = '';
    public $perPage     = 10;
    public $standards   = [];
    public $sections    = [];
    public $students    = [];

    protected $queryString = [
        'activeTab'  => ['except' => 'fee_structure'],
        'search'     => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->standards  = Standard::where('organization_id', $this->orgId())
            ->where('is_active', true)->orderBy('order')->get();
        $this->submitDate = today()->toDateString();
        $this->loadPenaltySettings();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    public function showTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->search = '';

        if ($tab === 'analytics') {
            $this->loadAnalytics();
        } elseif ($tab === 'payments') {
            $this->loadPaymentPeriodStats();
        } elseif ($tab === 'penalties') {
            $this->loadPenaltySettings();
            $this->loadPenaltyAnalytics();
        }
    }

    // ─── Fee Structure ─────────────────────────────────────────────────────────

    public function openStructureModal(int $id = null): void
    {
        $this->resetStructureForm();
        $this->editStructureId   = $id;
        $this->structureModalOpen = true;

        if ($id) {
            $s = FeeStructure::find($id);
            $this->structureStandardId = $s->standard_id;
            $this->structureSectionId  = $s->section_id;
            $this->feeName             = $s->fee_name;
            $this->feeAmount           = $s->amount;
            $this->structureFeeType    = $s->fee_type;
            $this->academicYear        = $s->academic_year;
        }
    }

    public function saveStructure(): void
    {
        $this->validate([
            'structureStandardId' => 'required|exists:standards,id',
            'feeName'             => 'required|string|max:255',
            'feeAmount'           => 'required|numeric|min:0',
            'structureFeeType'    => 'required|in:academic,transport',
            'academicYear'        => 'required|string|max:20',
        ]);

        $data = [
            'organization_id' => $this->orgId(),
            'standard_id'     => $this->structureStandardId,
            'section_id'      => $this->structureSectionId ?: null,
            'fee_name'        => $this->feeName,
            'amount'          => $this->feeAmount,
            'fee_type'        => $this->structureFeeType,
            'academic_year'   => $this->academicYear,
            'is_active'       => true,
        ];

        try {
            if ($this->editStructureId) {
                FeeStructure::find($this->editStructureId)->update($data);
                $this->notification()->success('Fee structure updated successfully!');
            } else {
                FeeStructure::create($data);
                $this->notification()->success('Fee structure added successfully!');
            }
            $this->resetStructureForm();
        } catch (\Exception $e) {
            $this->notification()->error('Error', $e->getMessage());
        }
    }

    public function deleteStructure(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Fee Structure?',
            'description' => 'This action cannot be undone.',
            'icon'        => 'error',
            'accept'      => ['label' => 'Yes, delete', 'method' => 'doDeleteStructure', 'params' => $id],
            'reject'      => ['label' => 'Cancel'],
        ]);
    }

    public function doDeleteStructure(int $id): void
    {
        FeeStructure::find($id)?->delete();
        $this->notification()->success('Fee structure deleted!');
    }

    private function resetStructureForm(): void
    {
        $this->reset([
            'editStructureId', 'structureModalOpen',
            'structureStandardId', 'structureSectionId',
            'feeName', 'feeAmount', 'structureFeeType', 'academicYear',
        ]);
    }

    public function updatedFilterStructureStandard(): void
    {
        $this->filterStructureSection = '';
        $this->sections = $this->filterStructureStandard
            ? Section::where('standard_id', $this->filterStructureStandard)->where('is_active', true)->get()
            : [];
        $this->resetPage();
    }

    // ─── Fee Submission ────────────────────────────────────────────────────────

    public function updatedSubmissionStandardId(): void
    {
        $this->submissionSectionId = '';
        $this->selectedStudentId   = '';
        $this->classStructures     = [];
        $this->studentTransactions = [];
        $this->students            = [];

        if ($this->submissionStandardId) {
            $this->sections = Section::where('standard_id', $this->submissionStandardId)
                ->where('is_active', true)->get();
            $this->loadSubmissionStudents();
        }
    }

    public function updatedSubmissionSectionId(): void
    {
        $this->selectedStudentId   = '';
        $this->classStructures     = [];
        $this->studentTransactions = [];
        $this->loadSubmissionStudents();
    }

    private function loadSubmissionStudents(): void
    {
        if (!$this->submissionStandardId) {
            $this->students = [];
            return;
        }

        $this->students = StudentDetail::with('user')
            ->where('organization_id', $this->orgId())
            ->where('standard_id', $this->submissionStandardId)
            ->when($this->submissionSectionId, fn($q) => $q->where('section_id', $this->submissionSectionId))
            ->get();
    }

    public function updatedSelectedStudentId(): void
    {
        if (!$this->selectedStudentId) {
            $this->classStructures     = [];
            $this->studentTransactions = [];
            return;
        }

        $student = StudentDetail::find($this->selectedStudentId);
        if (!$student) return;

        // Load fee structures for this student's class
        $this->classStructures = FeeStructure::where('organization_id', $this->orgId())
            ->where('standard_id', $student->standard_id)
            ->where(function ($q) use ($student) {
                $q->where('section_id', $student->section_id)->orWhereNull('section_id');
            })
            ->where('is_active', true)
            ->get()->toArray();

        // Load payment history for this student
        $this->studentTransactions = FeePayment::with(['standard', 'section'])
            ->where('organization_id', $this->orgId())
            ->where('student_detail_id', $this->selectedStudentId)
            ->orderByDesc('payment_date')
            ->get()->toArray();
    }

    public function submitFeePayment(): void
    {
        $this->validate([
            'selectedStudentId' => 'required|exists:student_details,id',
            'submitAmount'      => 'required|numeric|min:1',
            'submitFeeType'     => 'required|in:academic,transport',
            'submitPaymentMode' => 'required|in:cash,online,cheque,bank_transfer',
            'submitDate'        => 'required|date',
            'submittedBy'       => 'required|string|max:255',
        ]);

        try {
            $student = StudentDetail::find($this->selectedStudentId);

            FeePayment::create([
                'organization_id'   => $this->orgId(),
                'student_detail_id' => $this->selectedStudentId,
                'standard_id'       => $student->standard_id,
                'section_id'        => $student->section_id,
                'fee_type'          => $this->submitFeeType,
                'amount'            => $this->submitAmount,
                'payment_mode'      => $this->submitPaymentMode,
                'payment_date'      => $this->submitDate,
                'remark'            => $this->submitRemark,
                'submitted_by'      => $this->submittedBy,
            ]);

            $this->notification()->success('Fee submitted successfully!');
            $this->reset(['submitAmount', 'submitFeeType', 'submitPaymentMode', 'submitRemark', 'submittedBy']);
            $this->submitDate = today()->toDateString();

            // Refresh transactions
            $this->updatedSelectedStudentId();
        } catch (\Exception $e) {
            $this->notification()->error('Error submitting fee', $e->getMessage());
        }
    }

    // ─── View Fee ─────────────────────────────────────────────────────────────

    public function setViewSubTab(string $tab): void
    {
        $this->viewSubTab = $tab;
    }

    public function updatedViewStudentStandardId(): void
    {
        $this->viewStudentSectionId = '';
        $this->viewStudentId        = '';
        $this->studentFeeView       = [];
        $this->sections = $this->viewStudentStandardId
            ? Section::where('standard_id', $this->viewStudentStandardId)->where('is_active', true)->get()
            : [];
    }

    public function updatedViewStudentSectionId(): void
    {
        $this->viewStudentId  = '';
        $this->studentFeeView = [];
        if ($this->viewStudentStandardId) {
            $this->students = StudentDetail::with('user')
                ->where('organization_id', $this->orgId())
                ->where('standard_id', $this->viewStudentStandardId)
                ->when($this->viewStudentSectionId, fn($q) => $q->where('section_id', $this->viewStudentSectionId))
                ->get();
        }
    }

    public function loadStudentFeeView(): void
    {
        if (!$this->viewStudentId) return;

        $student = StudentDetail::with(['standard', 'section', 'user'])->find($this->viewStudentId);
        if (!$student) return;

        $structures = FeeStructure::where('organization_id', $this->orgId())
            ->where('standard_id', $student->standard_id)
            ->where(function ($q) use ($student) {
                $q->where('section_id', $student->section_id)->orWhereNull('section_id');
            })
            ->where('is_active', true)
            ->get();

        $payments = FeePayment::where('organization_id', $this->orgId())
            ->where('student_detail_id', $this->viewStudentId)
            ->orderByDesc('payment_date')
            ->get();

        $academicTotal    = $structures->where('fee_type', 'academic')->sum('amount');
        $transportTotal   = $student->transportation_required
            ? $structures->where('fee_type', 'transport')->sum('amount')
            : 0;
        $academicPaid     = $payments->where('fee_type', 'academic')->sum('amount');
        $transportPaid    = $payments->where('fee_type', 'transport')->sum('amount');
        $totalFee         = $academicTotal + $transportTotal;
        $totalPaid        = $academicPaid + $transportPaid;

        $this->studentFeeView = [
            'student'          => $student,
            'structures'       => $structures,
            'payments'         => $payments,
            'academicTotal'    => $academicTotal,
            'transportTotal'   => $transportTotal,
            'totalFee'         => $totalFee,
            'academicPaid'     => $academicPaid,
            'transportPaid'    => $transportPaid,
            'totalPaid'        => $totalPaid,
            'remaining'        => max(0, $totalFee - $totalPaid),
            'hasTransport'     => (bool) $student->transportation_required,
        ];
    }

    public function updatedViewClassStandardId(): void
    {
        $this->viewClassSectionId = '';
        $this->classFeeList       = [];
        $this->sections = $this->viewClassStandardId
            ? Section::where('standard_id', $this->viewClassStandardId)->where('is_active', true)->get()
            : [];
    }

    public function loadClassFeeView(): void
    {
        if (!$this->viewClassStandardId) return;

        $students = StudentDetail::with(['user', 'standard', 'section'])
            ->where('organization_id', $this->orgId())
            ->where('standard_id', $this->viewClassStandardId)
            ->when($this->viewClassSectionId, fn($q) => $q->where('section_id', $this->viewClassSectionId))
            ->get();

        $structures = FeeStructure::where('organization_id', $this->orgId())
            ->where('standard_id', $this->viewClassStandardId)
            ->where('is_active', true)
            ->get();

        $this->classFeeList = $students->map(function ($student) use ($structures) {
            $studentStructures = $structures->filter(function ($s) use ($student) {
                return is_null($s->section_id) || $s->section_id == $student->section_id;
            });

            $academicFee   = $studentStructures->where('fee_type', 'academic')->sum('amount');
            $transportFee  = $student->transportation_required
                ? $studentStructures->where('fee_type', 'transport')->sum('amount')
                : 0;

            $collected = FeePayment::where('organization_id', $this->orgId())
                ->where('student_detail_id', $student->id)
                ->sum('amount');

            return [
                'id'           => $student->id,
                'name'         => $student->user->name ?? '-',
                'admission_no' => $student->admission_no,
                'class'        => $student->standard->name ?? '-',
                'section'      => $student->section->name ?? '-',
                'academicFee'  => $academicFee,
                'transportFee' => $transportFee,
                'totalFee'     => $academicFee + $transportFee,
                'collected'    => $collected,
            ];
        })->values()->toArray();
    }

    // ─── Analytics ────────────────────────────────────────────────────────────

    public function updatedAnalyticsStandardId(): void
    {
        $this->analyticsSectionId = '';
        $this->sections = $this->analyticsStandardId
            ? Section::where('standard_id', $this->analyticsStandardId)->where('is_active', true)->get()
            : [];
        $this->loadAnalytics();
    }

    public function updatedAnalyticsSectionId(): void
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics(): void
    {
        $orgId = $this->orgId();

        $structureQuery = FeeStructure::where('organization_id', $orgId)->where('is_active', true);
        $paymentQuery   = FeePayment::where('organization_id', $orgId);

        if ($this->analyticsStandardId) {
            $structureQuery->where('standard_id', $this->analyticsStandardId);
            $paymentQuery->where('standard_id', $this->analyticsStandardId);
        }
        if ($this->analyticsSectionId) {
            $structureQuery->where(function ($q) {
                $q->where('section_id', $this->analyticsSectionId)->orWhereNull('section_id');
            });
            $paymentQuery->where('section_id', $this->analyticsSectionId);
        }

        $academicTotal   = (clone $structureQuery)->where('fee_type', 'academic')->sum('amount');
        $transportTotal  = (clone $structureQuery)->where('fee_type', 'transport')->sum('amount');
        $totalCollected  = (clone $paymentQuery)->sum('amount');
        $academicPaid    = (clone $paymentQuery)->where('fee_type', 'academic')->sum('amount');
        $transportPaid   = (clone $paymentQuery)->where('fee_type', 'transport')->sum('amount');

        $this->analyticsData = [
            'totalFee'       => $academicTotal + $transportTotal,
            'academicTotal'  => $academicTotal,
            'transportTotal' => $transportTotal,
            'collected'      => $totalCollected,
            'academicPaid'   => $academicPaid,
            'transportPaid'  => $transportPaid,
            'remaining'      => max(0, ($academicTotal + $transportTotal) - $totalCollected),
        ];

        // Student list
        $studentQuery = StudentDetail::with(['user', 'standard', 'section'])
            ->where('organization_id', $orgId);
        if ($this->analyticsStandardId) {
            $studentQuery->where('standard_id', $this->analyticsStandardId);
        }
        if ($this->analyticsSectionId) {
            $studentQuery->where('section_id', $this->analyticsSectionId);
        }

        $structures = FeeStructure::where('organization_id', $orgId)->where('is_active', true)
            ->when($this->analyticsStandardId, fn($q) => $q->where('standard_id', $this->analyticsStandardId))
            ->get();

        $this->analyticsStudentList = $studentQuery->get()->map(function ($student) use ($structures, $orgId) {
            $studentStructures = $structures->filter(function ($s) use ($student) {
                return $s->standard_id == $student->standard_id &&
                    (is_null($s->section_id) || $s->section_id == $student->section_id);
            });

            $academicFee  = $studentStructures->where('fee_type', 'academic')->sum('amount');
            $transportFee = $student->transportation_required
                ? $studentStructures->where('fee_type', 'transport')->sum('amount')
                : 0;
            $collected    = FeePayment::where('organization_id', $orgId)
                ->where('student_detail_id', $student->id)->sum('amount');

            return [
                'id'           => $student->id,
                'name'         => $student->user->name ?? '-',
                'admission_no' => $student->admission_no,
                'class'        => $student->standard->name ?? '-',
                'section'      => $student->section->name ?? '-',
                'totalFee'     => $academicFee + $transportFee,
                'collected'    => $collected,
            ];
        })->values()->toArray();
    }

    // ─── Payments ─────────────────────────────────────────────────────────────

    public function updatedPaymentStandardId(): void
    {
        $this->paymentSectionId = '';
        $this->sections = $this->paymentStandardId
            ? Section::where('standard_id', $this->paymentStandardId)->where('is_active', true)->get()
            : [];
        $this->loadPaymentPeriodStats();
        $this->resetPage();
    }

    public function updatedPaymentSectionId(): void
    {
        $this->loadPaymentPeriodStats();
        $this->resetPage();
    }

    public function updatedPaymentModeFilter(): void
    {
        $this->resetPage();
    }

    public function loadPaymentPeriodStats(): void
    {
        $orgId = $this->orgId();
        $base  = FeePayment::where('organization_id', $orgId)
            ->when($this->paymentStandardId, fn($q) => $q->where('standard_id', $this->paymentStandardId))
            ->when($this->paymentSectionId, fn($q) => $q->where('section_id', $this->paymentSectionId));

        $today     = today();
        $yesterday = today()->subDay();

        $this->paymentPeriodStats = [
            'today'      => (clone $base)->whereDate('payment_date', $today)->sum('amount'),
            'yesterday'  => (clone $base)->whereDate('payment_date', $yesterday)->sum('amount'),
            'this_week'  => (clone $base)->whereBetween('payment_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->sum('amount'),
            'this_month' => (clone $base)->whereMonth('payment_date', $today->month)->whereYear('payment_date', $today->year)->sum('amount'),
            'last_month' => (clone $base)->whereMonth('payment_date', $today->copy()->subMonth()->month)->whereYear('payment_date', $today->copy()->subMonth()->year)->sum('amount'),
        ];
    }

    private function getPaymentsQuery()
    {
        return FeePayment::with(['studentDetail.user', 'standard', 'section'])
            ->where('organization_id', $this->orgId())
            ->when($this->paymentStandardId, fn($q) => $q->where('standard_id', $this->paymentStandardId))
            ->when($this->paymentSectionId, fn($q) => $q->where('section_id', $this->paymentSectionId))
            ->when($this->paymentModeFilter, fn($q) => $q->where('payment_mode', $this->paymentModeFilter))
            ->when($this->search, fn($q) => $q->whereHas('studentDetail.user', fn($q) => $q->where('name', 'like', "%{$this->search}%")));
    }

    // ─── Penalties ────────────────────────────────────────────────────────────

    public function loadPenaltySettings(): void
    {
        $settings            = FeeSettings::getForOrg($this->orgId());
        $this->penaltyPerDay  = $settings->penalty_per_day;
        $this->cycleType      = $settings->cycle_type;
        $this->dueDayOfMonth  = $settings->due_day_of_month;
    }

    public function saveSettings(): void
    {
        $this->validate([
            'penaltyPerDay'   => 'required|numeric|min:0',
            'cycleType'       => 'required|in:monthly,quarterly',
            'dueDayOfMonth'   => 'required|integer|min:1|max:31',
        ]);

        FeeSettings::updateOrCreate(
            ['organization_id' => $this->orgId()],
            [
                'penalty_per_day'  => $this->penaltyPerDay,
                'cycle_type'       => $this->cycleType,
                'due_day_of_month' => $this->dueDayOfMonth,
                'is_active'        => true,
            ]
        );

        $this->notification()->success('Fee settings saved successfully!');
        $this->loadPenaltyAnalytics();
    }

    public function loadPenaltyAnalytics(): void
    {
        $orgId    = $this->orgId();
        $settings = FeeSettings::getForOrg($orgId);

        if ($settings->penalty_per_day <= 0) {
            $this->penaltyAnalytics = ['total' => 0, 'students' => 0, 'avg_days_overdue' => 0];
            return;
        }

        // Calculate penalty: students who haven't paid and are past due day of month
        $dueDay  = $settings->due_day_of_month;
        $today   = Carbon::today();
        $dueDate = Carbon::createFromDate($today->year, $today->month, min($dueDay, $today->daysInMonth));

        if ($today->day <= $dueDay) {
            // Not yet past due date this month
            $dueDate = $dueDate->subMonth();
        }

        // Students with any unpaid fee (simplified: those who haven't paid anything this month)
        $studentCount = StudentDetail::where('organization_id', $orgId)->count();
        $paidThisMonth = FeePayment::where('organization_id', $orgId)
            ->whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->distinct('student_detail_id')
            ->count('student_detail_id');

        $overdueStudents = max(0, $studentCount - $paidThisMonth);
        $daysOverdue     = max(0, $today->diffInDays($dueDate));
        $totalPenalty    = $overdueStudents * $daysOverdue * $settings->penalty_per_day;

        $this->penaltyAnalytics = [
            'total'            => $totalPenalty,
            'students'         => $overdueStudents,
            'days_overdue'     => $daysOverdue,
            'penalty_per_day'  => $settings->penalty_per_day,
        ];
    }

    // ─── Shared ───────────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $orgId = $this->orgId();
        $data  = ['standards' => $this->standards, 'sections' => $this->sections, 'students' => $this->students];

        if ($this->activeTab === 'fee_structure') {
            $data['structures'] = FeeStructure::with(['standard', 'section'])
                ->where('organization_id', $orgId)
                ->when($this->filterStructureStandard, fn($q) => $q->where('standard_id', $this->filterStructureStandard))
                ->when($this->filterStructureSection, fn($q) => $q->where('section_id', $this->filterStructureSection))
                ->when($this->filterStructureYear, fn($q) => $q->where('academic_year', $this->filterStructureYear))
                ->when($this->search, fn($q) => $q->where('fee_name', 'like', "%{$this->search}%"))
                ->orderByDesc('created_at')
                ->paginate($this->perPage);
        }

        if ($this->activeTab === 'payments') {
            $data['payments'] = $this->getPaymentsQuery()
                ->orderByDesc('payment_date')
                ->paginate($this->perPage);
        }

        return view('livewire.admin.fee', $data);
    }
}
