<?php

namespace App\Livewire\Accounts;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Admin\Fee\FeeStructure;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReportCard extends Component
{
    use WithPagination;

    public $filterStandardId = '';
    public $filterSectionId = '';
    public $filterStatus = '';
    public $search = '';
    public $perPage = 15;

    // Analytics
    public $analytics = [];

    // UI state
    public bool $showReportCardNotice = false;

    public function mount(): void
    {
        $this->loadAnalytics();
    }

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    public function loadAnalytics(): void
    {
        $orgId = $this->orgId();

        $totalStudents = StudentDetail::where('organization_id', $orgId)->count();
        $totalStructure = FeeStructure::where('organization_id', $orgId)->where('is_active', true)->sum('amount');
        $totalCollected = FeePayment::where('organization_id', $orgId)->sum('amount');
        $totalPending = max(0, $totalStructure - $totalCollected);

        // Students with full payment
        $students = StudentDetail::where('organization_id', $orgId)->get();
        $structures = FeeStructure::where('organization_id', $orgId)->where('is_active', true)->get();

        $paidFull = 0;
        $paidPartial = 0;
        $unpaid = 0;

        foreach ($students as $student) {
            $studentStructures = $structures->filter(function ($s) use ($student) {
                return $s->standard_id == $student->standard_id
                    && (is_null($s->section_id) || $s->section_id == $student->section_id);
            });

            $academicFee = $studentStructures->where('fee_type', 'academic')->sum('amount');
            $transportFee = $student->transportation_required
                ? $studentStructures->where('fee_type', 'transport')->sum('amount')
                : 0;
            $totalFee = $academicFee + $transportFee;

            $paid = FeePayment::where('organization_id', $orgId)
                ->where('student_detail_id', $student->id)
                ->sum('amount');

            if ($totalFee > 0 && $paid >= $totalFee) {
                $paidFull++;
            } elseif ($paid > 0) {
                $paidPartial++;
            } else {
                $unpaid++;
            }
        }

        $this->analytics = [
            'totalStudents' => $totalStudents,
            'totalFee' => $totalStructure,
            'totalCollected' => $totalCollected,
            'totalPending' => $totalPending,
            'paidFull' => $paidFull,
            'paidPartial' => $paidPartial,
            'unpaid' => $unpaid,
            'collectionRate' => $totalStructure > 0 ? round(($totalCollected / $totalStructure) * 100, 1) : 0,
        ];
    }

    public function issueReportCard(int $studentId): void
    {
        // Placeholder — feature coming soon
        $this->showReportCardNotice = true;
    }

    public function updatedFilterStandardId(): void
    {
        $this->filterSectionId = '';
        $this->resetPage();
    }

    public function updatedFilterSectionId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $orgId = $this->orgId();

        $standards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)->orderBy('order')->get();

        $sections = collect();
        if ($this->filterStandardId) {
            $sections = Section::where('standard_id', $this->filterStandardId)
                ->where('organization_id', $orgId)->where('is_active', true)->get();
        }

        // Build student report list
        $studentQuery = StudentDetail::with(['user', 'standard', 'section'])
            ->where('organization_id', $orgId)
            ->when($this->filterStandardId, fn($q) => $q->where('standard_id', $this->filterStandardId))
            ->when($this->filterSectionId, fn($q) => $q->where('section_id', $this->filterSectionId))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('admission_no', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"));
                });
            });

        $allStudents = $studentQuery->get();
        $structures = FeeStructure::where('organization_id', $orgId)->where('is_active', true)->get();

        $reportList = $allStudents->map(function ($student) use ($structures, $orgId) {
            $studentStructures = $structures->filter(function ($s) use ($student) {
                return $s->standard_id == $student->standard_id
                    && (is_null($s->section_id) || $s->section_id == $student->section_id);
            });

            $academicFee = $studentStructures->where('fee_type', 'academic')->sum('amount');
            $transportFee = $student->transportation_required
                ? $studentStructures->where('fee_type', 'transport')->sum('amount')
                : 0;
            $totalFee = $academicFee + $transportFee;

            $paid = FeePayment::where('organization_id', $orgId)
                ->where('student_detail_id', $student->id)
                ->sum('amount');

            if ($totalFee > 0 && $paid >= $totalFee) {
                $status = 'paid';
            } elseif ($paid > 0) {
                $status = 'partial';
            } else {
                $status = 'unpaid';
            }

            return [
                'id' => $student->id,
                'name' => $student->user->name ?? $student->full_name ?? '-',
                'admission_no' => $student->admission_no,
                'class' => $student->standard->name ?? '-',
                'section' => $student->section->name ?? '-',
                'totalFee' => $totalFee,
                'paid' => $paid,
                'pending' => max(0, $totalFee - $paid),
                'status' => $status,
            ];
        });

        // Filter by status
        if ($this->filterStatus) {
            $reportList = $reportList->where('status', $this->filterStatus);
        }

        $reportList = $reportList->values();

        return view('livewire.accounts.report-card', [
            'standards' => $standards,
            'sections' => $sections,
            'reportList' => $reportList,
        ]);
    }
}
