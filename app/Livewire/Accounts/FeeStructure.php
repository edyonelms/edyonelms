<?php

namespace App\Livewire\Accounts;

use App\Models\Admin\Fee\FeeStructure as FeeStructureModel;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class FeeStructure extends Component
{
    use WireUiActions, WithPagination;

    // ─── Form fields ────────────────────────────────────────────────────────────
    public $structureStandardId = '';
    public $structureSectionIds = [];
    public $structureFeeType = 'academic';
    public $academicYear = '';
    public $editStructureId = null;
    public $structureModalOpen = false;
    public $feeRows = [];
    public $transportMonthlyFee = '';

    // ─── Filters ────────────────────────────────────────────────────────────────
    public $filterStructureStandard = '';
    public $filterStructureSection = '';
    public $filterStructureType = '';

    // ─── View modal ─────────────────────────────────────────────────────────────
    public $viewModalOpen = false;
    public $viewStructureData = [];

    // ─── Print ──────────────────────────────────────────────────────────────────
    public $printModalOpen = false;
    public $printData = [];

    // ─── Delete confirm ─────────────────────────────────────────────────────────
    public ?int $pendingDeleteStructureId = null;

    // ─── Tabs ───────────────────────────────────────────────────────────────────
    public string $activeTab = 'academic';

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStructureType' => ['except' => ''],
    ];

    // Transport months: April 2026 to March 2027, excluding June
    public array $transportMonths = [
        'April 2026', 'May 2026', 'July 2026', 'August 2026',
        'September 2026', 'October 2026', 'November 2026', 'December 2026',
        'January 2027', 'February 2027', 'March 2027',
    ];

    public function mount(): void
    {
        $this->academicYear = '2026-27';
        $this->feeRows = [['name' => '', 'amount' => '']];
    }

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    // ─── Tabs ───────────────────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ─── Fee Row management ─────────────────────────────────────────────────────

    public function addFeeRow(): void
    {
        $this->feeRows[] = ['name' => '', 'amount' => ''];
    }

    public function removeFeeRow(int $index): void
    {
        if (count($this->feeRows) > 1) {
            unset($this->feeRows[$index]);
            $this->feeRows = array_values($this->feeRows);
        }
    }

    // ─── Modal open/close ───────────────────────────────────────────────────────

    public function openStructureModal(?int $id = null): void
    {
        $this->resetStructureForm();
        $this->editStructureId = $id;
        $this->structureModalOpen = true;

        if ($id) {
            $s = FeeStructureModel::find($id);
            if (!$s) return;

            $this->structureStandardId = $s->standard_id;
            $this->structureSectionIds = $s->section_id ? [$s->section_id] : [];
            $this->structureFeeType = $s->fee_type;
            $this->academicYear = $s->academic_year;

            if ($s->fee_type === 'transport') {
                $this->transportMonthlyFee = round($s->amount / 11, 2);
            } else {
                $this->feeRows = [['name' => $s->fee_name, 'amount' => $s->amount]];
            }
        }
    }

    // ─── Save ───────────────────────────────────────────────────────────────────

    public function saveStructure(): void
    {
        $this->validate([
            'structureStandardId' => 'required|exists:standards,id',
            'structureFeeType' => 'required|in:academic,transport',
            'academicYear' => 'required|string|max:20',
        ]);

        if ($this->structureFeeType === 'transport') {
            $this->validate([
                'transportMonthlyFee' => 'required|numeric|min:0',
            ]);
        } else {
            $this->validate([
                'feeRows' => 'required|array|min:1',
                'feeRows.*.name' => 'required|string|max:255',
                'feeRows.*.amount' => 'required|numeric|min:0',
            ]);
        }

        try {
            $sectionIds = !empty($this->structureSectionIds) ? $this->structureSectionIds : [null];

            if ($this->editStructureId) {
                $record = FeeStructureModel::find($this->editStructureId);
                if ($this->structureFeeType === 'transport') {
                    $totalAmount = round($this->transportMonthlyFee * 11, 2);
                    $record->update([
                        'standard_id' => $this->structureStandardId,
                        'section_id' => !empty($this->structureSectionIds) ? $this->structureSectionIds[0] : null,
                        'fee_name' => 'Transport Fee',
                        'amount' => $totalAmount,
                        'fee_type' => 'transport',
                        'academic_year' => $this->academicYear,
                    ]);
                } else {
                    $row = $this->feeRows[0] ?? ['name' => '', 'amount' => 0];
                    $record->update([
                        'standard_id' => $this->structureStandardId,
                        'section_id' => !empty($this->structureSectionIds) ? $this->structureSectionIds[0] : null,
                        'fee_name' => $row['name'],
                        'amount' => $row['amount'],
                        'fee_type' => 'academic',
                        'academic_year' => $this->academicYear,
                    ]);
                }
                $this->notification()->success('Fee structure updated successfully!');
            } else {
                foreach ($sectionIds as $sectionId) {
                    if ($this->structureFeeType === 'transport') {
                        $totalAmount = round($this->transportMonthlyFee * 11, 2);
                        FeeStructureModel::create([
                            'organization_id' => $this->orgId(),
                            'standard_id' => $this->structureStandardId,
                            'section_id' => $sectionId,
                            'fee_name' => 'Transport Fee',
                            'amount' => $totalAmount,
                            'fee_type' => 'transport',
                            'academic_year' => $this->academicYear,
                            'is_active' => true,
                        ]);
                    } else {
                        foreach ($this->feeRows as $row) {
                            FeeStructureModel::create([
                                'organization_id' => $this->orgId(),
                                'standard_id' => $this->structureStandardId,
                                'section_id' => $sectionId,
                                'fee_name' => $row['name'],
                                'amount' => $row['amount'],
                                'fee_type' => 'academic',
                                'academic_year' => $this->academicYear,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
                $this->notification()->success('Fee structure added successfully!');
            }

            $this->resetStructureForm();
        } catch (\Exception $e) {
            $this->notification()->error('Error', $e->getMessage());
        }
    }

    // ─── Edit ───────────────────────────────────────────────────────────────────

    public function editStructure(int $id): void
    {
        $this->openStructureModal($id);
    }

    // ─── View ───────────────────────────────────────────────────────────────────

    public function viewStructure(int $id): void
    {
        $structure = FeeStructureModel::with(['standard', 'section'])->find($id);
        if (!$structure) return;

        $this->viewStructureData = [
            'id' => $structure->id,
            'class' => $structure->standard->name ?? '-',
            'section' => $structure->section->name ?? 'All Sections',
            'fee_type' => $structure->fee_type,
            'fee_name' => $structure->fee_name,
            'amount' => $structure->amount,
            'academic_year' => $structure->academic_year,
            'monthly_fee' => $structure->fee_type === 'transport' ? round($structure->amount / 11, 2) : null,
        ];
        $this->viewModalOpen = true;
    }

    // ─── Print ──────────────────────────────────────────────────────────────────

    public function printStructure(int $id): void
    {
        $structure = FeeStructureModel::with(['standard', 'section'])->find($id);
        if (!$structure) return;

        $academicFees = FeeStructureModel::with(['standard', 'section'])
            ->where('organization_id', $this->orgId())
            ->where('standard_id', $structure->standard_id)
            ->where('fee_type', 'academic')
            ->when($structure->section_id, fn($q) => $q->where('section_id', $structure->section_id))
            ->get();

        $transportFee = FeeStructureModel::where('organization_id', $this->orgId())
            ->where('standard_id', $structure->standard_id)
            ->where('fee_type', 'transport')
            ->when($structure->section_id, fn($q) => $q->where('section_id', $structure->section_id))
            ->first();

        $this->printData = [
            'class' => $structure->standard->name ?? '-',
            'section' => $structure->section->name ?? 'All Sections',
            'academic_year' => $structure->academic_year,
            'academic_fees' => $academicFees->map(fn($f) => [
                'fee_name' => $f->fee_name,
                'amount' => $f->amount,
            ])->toArray(),
            'academic_total' => $academicFees->sum('amount'),
            'transport_fee' => $transportFee ? $transportFee->amount : 0,
            'transport_monthly' => $transportFee ? round($transportFee->amount / 11, 2) : 0,
        ];
        $this->printModalOpen = true;
    }

    // ─── Delete (custom pendingDelete pattern) ───────────────────────────────────

    public function deleteStructure(int $id): void
    {
        $this->pendingDeleteStructureId = $id;
    }

    public function cancelDeleteStructure(): void
    {
        $this->pendingDeleteStructureId = null;
    }

    public function doDeleteStructure(): void
    {
        FeeStructureModel::where('id', $this->pendingDeleteStructureId)
            ->where('organization_id', $this->orgId())
            ->delete();
        $this->pendingDeleteStructureId = null;
        $this->notification()->success('Fee structure deleted!');
    }

    // ─── Reset ──────────────────────────────────────────────────────────────────

    private function resetStructureForm(): void
    {
        $this->reset([
            'editStructureId', 'structureModalOpen',
            'structureStandardId', 'structureSectionIds',
            'structureFeeType', 'transportMonthlyFee',
        ]);
        $this->academicYear = '2026-27';
        $this->feeRows = [['name' => '', 'amount' => '']];
    }

    // ─── Filter watchers ────────────────────────────────────────────────────────

    public function updatedStructureStandardId(): void
    {
        $this->structureSectionIds = [];
    }

    public function updatedFilterStructureStandard(): void
    {
        $this->filterStructureSection = '';
        $this->resetPage();
    }

    public function updatedFilterStructureSection(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStructureType(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        $orgId = $this->orgId();

        $standards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)->orderBy('order')->get();

        // Sections for filter dropdown
        $sections = [];
        if ($this->filterStructureStandard) {
            $sections = Section::where('standard_id', $this->filterStructureStandard)
                ->where('organization_id', $orgId)
                ->where('is_active', true)->get();
        }

        // Sections for the form modal
        $formSections = [];
        if ($this->structureStandardId) {
            $formSections = Section::where('standard_id', $this->structureStandardId)
                ->where('organization_id', $orgId)
                ->where('is_active', true)->get();
        }

        // Analytics
        $totalStructures = FeeStructureModel::where('organization_id', $orgId)->count();
        $academicCount = FeeStructureModel::where('organization_id', $orgId)->where('fee_type', 'academic')->count();
        $transportCount = FeeStructureModel::where('organization_id', $orgId)->where('fee_type', 'transport')->count();
        $totalAcademicAmt = FeeStructureModel::where('organization_id', $orgId)->where('fee_type', 'academic')->sum('amount');

        // ── Build query filtered by active tab ──────────────────────────────────
        $tabFeeType = $this->activeTab; // 'academic' or 'transport'

        $structuresQuery = FeeStructureModel::with(['standard', 'section'])
            ->where('organization_id', $orgId)
            ->where('fee_type', $tabFeeType)
            ->when($this->filterStructureStandard, fn($q) => $q->where('standard_id', $this->filterStructureStandard))
            ->when($this->filterStructureSection, fn($q) => $q->where('section_id', $this->filterStructureSection))
            ->when($this->search && $tabFeeType === 'academic', fn($q) => $q->where('fee_name', 'like', "%{$this->search}%"))
            ->orderBy('standard_id')
            ->orderBy('section_id');

        $structures = $structuresQuery->paginate($this->perPage);

        // Transport monthly data (for transport tab)
        $transportMonthlyData = [];
        if ($tabFeeType === 'transport') {
            foreach ($structures as $item) {
                $monthlyFee = round($item->amount / 11, 2);
                $transportMonthlyData[] = [
                    'id' => $item->id,
                    'class' => $item->standard->name ?? '-',
                    'section' => $item->section->name ?? 'All',
                    'monthly_fee' => $monthlyFee,
                    'total' => $item->amount,
                    'academic_year' => $item->academic_year,
                ];
            }
        }

        return view('livewire.accounts.fee-structure', [
            'standards' => $standards,
            'sections' => $sections,
            'formSections' => $formSections,
            'structures' => $structures,
            'transportMonthlyData' => $transportMonthlyData,
            'totalStructures' => $totalStructures,
            'academicCount' => $academicCount,
            'transportCount' => $transportCount,
            'totalAcademicAmt' => $totalAcademicAmt,
        ]);
    }
}
