<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Certificate;
use App\Models\Admin\TransferCertificate;
use App\Models\Student\StudentDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class TcCertificate extends Component
{
    use WireUiActions, WithPagination;

    // ─── Tabs: achievement | participation | tc ───────────────
    #[Url(keep: true)]
    public string $activeTab = 'achievement';

    // ─── Modals ───────────────────────────────────────────────
    public bool $certModal    = false;
    public bool $tcModal      = false;
    public bool $previewModal = false;

    public ?int   $editCertId    = null;
    public ?int   $editTcId      = null;
    public ?int   $previewId     = null;
    public string $previewType   = 'cert'; // cert | tc

    public string $type                  = 'achievement';
    public ?int   $student_detail_id     = null;
    public string $event_name            = '';
    public string $issued_by             = '';
    public string $issued_by_designation = '';
    public string $description           = '';
    public string $issued_date           = '';

    public ?int   $tc_student_id            = null;
    public string $book_no                  = '';
    public string $nationality              = 'Indian';
    public bool   $is_sc_st                 = false;
    public string $last_class_studied       = '';
    public string $exam_last_taken          = '';
    public string $whether_failed           = 'No';
    public string $subjects_studied         = '';
    public string $qualified_for_promotion  = 'Yes';
    public string $fees_paid_upto           = '';
    public string $fee_concession           = '';
    public int    $total_working_days       = 0;
    public int    $days_present             = 0;
    public string $is_ncc_scout             = 'No';
    public string $extra_activities         = '';
    public string $general_conduct          = 'Good';
    public string $application_date         = '';
    public string $tc_issue_date            = '';
    public string $reason_for_leaving       = '';
    public string $tc_remarks               = '';

    // ─── Filters ──────────────────────────────────────────────
    #[Url(keep: true)]
    public string $search  = '';
    #[Url(keep: true)]
    public int    $perPage = 10;

    // ─── Dropdown data (plain arrays — safe for Livewire) ─────
    public array $students       = [];
    public array $conductOptions = ['Excellent', 'Good', 'Satisfactory', 'Poor'];
    public array $failedOptions  = ['No', 'Once', 'Twice'];
    public array $nccOptions     = ['No', 'NCC Cadet', 'Boy Scout', 'Girl Guide'];
    public $organization;

    public function mount(): void
    {
        $this->issued_date      = now()->format('Y-m-d');
        $this->application_date = now()->format('Y-m-d');
        $this->tc_issue_date    = now()->format('Y-m-d');
        $this->loadStudents();
        $this->organization = Auth::user()?->organization;
    }

    #[Computed]
    public function organizationId(): ?int
    {
        return Auth::user()?->organization_id;
    }

    #[Computed]
    public function statistics(): array
    {
        $orgId = $this->organizationId;
        if (!$orgId) return ['achievement' => 0, 'participation' => 0, 'tc' => 0];

        return [
            'achievement'   => Certificate::where('organization_id', $orgId)->where('type', 'achievement')->count(),
            'participation' => Certificate::where('organization_id', $orgId)->where('type', 'participation')->count(),
            'tc'            => TransferCertificate::where('organization_id', $orgId)->count(),
        ];
    }

    private function loadStudents(): void
    {
        if (!$this->organizationId) {
            $this->students = [];
            return;
        }

        $this->students = StudentDetail::where('organization_id', $this->organizationId)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'admission_no'])
            ->map(fn($s) => [
                'id'           => $s->id,
                'full_name'    => $s->full_name,
                'admission_no' => $s->admission_no,
            ])
            ->toArray();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
        $this->search = '';
        if (in_array($this->activeTab, ['achievement', 'participation'])) {
            $this->type = $this->activeTab;
        }
    }

    public function createCert(): void
    {
        $this->resetCertForm();
        $this->editCertId = null;
        $this->type       = in_array($this->activeTab, ['achievement', 'participation'])
            ? $this->activeTab : 'achievement';
        $this->certModal  = true;
    }

    public function editCert(int $id): void
    {
        $c = Certificate::findOrFail($id);
        $this->editCertId            = $c->id;
        $this->type                  = $c->type;
        $this->student_detail_id     = $c->student_detail_id;
        $this->event_name            = $c->event_name;
        $this->issued_by             = $c->issued_by;
        $this->issued_by_designation = $c->issued_by_designation ?? '';
        $this->description           = $c->description ?? '';
        $this->issued_date           = $c->issued_date->format('Y-m-d');
        $this->certModal             = true;
    }

    public function saveCert(): void
    {
        $this->validate([
            'type'                  => 'required|in:achievement,participation',
            'student_detail_id'     => 'required|exists:student_details,id',
            'event_name'            => 'required|string|max:255',
            'issued_by'             => 'required|string|max:255',
            'issued_by_designation' => 'nullable|string|max:100',
            'description'           => 'nullable|string|max:1000',
            'issued_date'           => 'required|date',
        ]);

        $data = [
            'organization_id'       => $this->organizationId,
            'student_detail_id'     => $this->student_detail_id,
            'type'                  => $this->type,
            'event_name'            => $this->event_name,
            'issued_by'             => $this->issued_by,
            'issued_by_designation' => $this->issued_by_designation ?: null,
            'description'           => $this->description ?: null,
            'issued_date'           => $this->issued_date,
        ];

        try {
            $this->editCertId
                ? Certificate::findOrFail($this->editCertId)->update($data)
                : Certificate::create($data);

            unset($this->statistics);
            $this->notification()->success(
                title: 'Success!',
                description: $this->editCertId ? 'Certificate updated.' : 'Certificate issued successfully.'
            );
            $this->closeCertModal();
        } catch (\Exception $e) {
            $this->notification()->error(title: 'Error!', description: $e->getMessage());
        }
    }

    public function deleteCert(int $id): void
    {
        $this->dialog()->confirm([
            'title'  => 'Delete Certificate?',
            'icon'   => 'error',
            'accept' => ['label' => 'Yes, Delete', 'method' => 'confirmDeleteCert', 'params' => $id],
            'reject' => ['label' => 'Cancel'],
        ]);
    }

    public function confirmDeleteCert(int $id): void
    {
        Certificate::findOrFail($id)->delete();
        unset($this->statistics);
        $this->notification()->success(title: 'Deleted!', description: 'Certificate removed.');
    }

    public function closeCertModal(): void
    {
        $this->certModal  = false;
        $this->editCertId = null;
        $this->resetCertForm();
        $this->resetValidation();
    }

    private function resetCertForm(): void
    {
        $this->reset(['student_detail_id', 'event_name', 'issued_by', 'issued_by_designation', 'description']);
        $this->type        = $this->activeTab === 'participation' ? 'participation' : 'achievement';
        $this->issued_date = now()->format('Y-m-d');
    }

    public function createTc(): void
    {
        $this->resetTcForm();
        $this->editTcId = null;
        $this->tcModal  = true;
    }

    public function editTc(int $id): void
    {
        $tc = TcCertificate::findOrFail($id);

        $this->editTcId                = $tc->id;
        $this->tc_student_id           = $tc->student_detail_id;
        $this->book_no                 = $tc->book_no ?? '';
        $this->nationality             = $tc->nationality;
        $this->is_sc_st                = $tc->is_sc_st;
        $this->last_class_studied      = $tc->last_class_studied ?? '';
        $this->exam_last_taken         = $tc->exam_last_taken ?? '';
        $this->whether_failed          = $tc->whether_failed;
        $this->subjects_studied        = $tc->subjects_studied ?? '';
        $this->qualified_for_promotion = $tc->qualified_for_promotion;
        $this->fees_paid_upto          = $tc->fees_paid_upto ?? '';
        $this->fee_concession          = $tc->fee_concession ?? '';
        $this->total_working_days      = $tc->total_working_days;
        $this->days_present            = $tc->days_present;
        $this->is_ncc_scout            = $tc->is_ncc_scout;
        $this->extra_activities        = $tc->extra_activities ?? '';
        $this->general_conduct         = $tc->general_conduct;
        $this->application_date        = $tc->application_date->format('Y-m-d');
        $this->tc_issue_date           = $tc->issue_date->format('Y-m-d');
        $this->reason_for_leaving      = $tc->reason_for_leaving ?? '';
        $this->tc_remarks              = $tc->remarks ?? '';
        $this->tcModal                 = true;
    }

    public function saveTc(): void
    {
        $this->validate([
            'tc_student_id'    => 'required|exists:student_details,id',
            'application_date' => 'required|date',
            'tc_issue_date'    => 'required|date',
            'general_conduct'  => 'required|string',
        ]);

        $data = [
            'organization_id'        => $this->organizationId,
            'student_detail_id'      => $this->tc_student_id,
            'book_no'                => $this->book_no ?: null,
            'nationality'            => $this->nationality,
            'is_sc_st'               => $this->is_sc_st,
            'last_class_studied'     => $this->last_class_studied ?: null,
            'exam_last_taken'        => $this->exam_last_taken ?: null,
            'whether_failed'         => $this->whether_failed,
            'subjects_studied'       => $this->subjects_studied ?: null,
            'qualified_for_promotion' => $this->qualified_for_promotion,
            'fees_paid_upto'         => $this->fees_paid_upto ?: null,
            'fee_concession'         => $this->fee_concession ?: null,
            'total_working_days'     => $this->total_working_days,
            'days_present'           => $this->days_present,
            'is_ncc_scout'           => $this->is_ncc_scout,
            'extra_activities'       => $this->extra_activities ?: null,
            'general_conduct'        => $this->general_conduct,
            'application_date'       => $this->application_date,
            'issue_date'             => $this->tc_issue_date,
            'reason_for_leaving'     => $this->reason_for_leaving ?: null,
            'remarks'                => $this->tc_remarks ?: null,
        ];

        try {
            $this->editTcId
                ? TransferCertificate::findOrFail($this->editTcId)->update($data)
                : TransferCertificate::create($data);

            unset($this->statistics);
            $this->notification()->success(
                title: 'Success!',
                description: $this->editTcId ? 'TC updated.' : 'Transfer Certificate issued.'
            );
            $this->closeTcModal();
        } catch (\Exception $e) {
            $this->notification()->error(title: 'Error!', description: $e->getMessage());
        }
    }

    public function deleteTc(int $id): void
    {
        $this->dialog()->confirm([
            'title'  => 'Delete Transfer Certificate?',
            'icon'   => 'error',
            'accept' => ['label' => 'Yes, Delete', 'method' => 'confirmDeleteTc', 'params' => $id],
            'reject' => ['label' => 'Cancel'],
        ]);
    }

    public function confirmDeleteTc(int $id): void
    {
        TransferCertificate::findOrFail($id)->delete();
        unset($this->statistics);
        $this->notification()->success(title: 'Deleted!', description: 'TC removed.');
    }

    public function closeTcModal(): void
    {
        $this->tcModal  = false;
        $this->editTcId = null;
        $this->resetTcForm();
        $this->resetValidation();
    }

    private function resetTcForm(): void
    {
        $this->reset([
            'tc_student_id',
            'book_no',
            'last_class_studied',
            'exam_last_taken',
            'subjects_studied',
            'fees_paid_upto',
            'fee_concession',
            'extra_activities',
            'reason_for_leaving',
            'tc_remarks',
        ]);
        $this->nationality             = 'Indian';
        $this->is_sc_st                = false;
        $this->whether_failed          = 'No';
        $this->qualified_for_promotion = 'Yes';
        $this->total_working_days      = 0;
        $this->days_present            = 0;
        $this->is_ncc_scout            = 'No';
        $this->general_conduct         = 'Good';
        $this->application_date        = now()->format('Y-m-d');
        $this->tc_issue_date           = now()->format('Y-m-d');
    }

    public function previewCert(int $id): void
    {
        $this->previewId   = $id;
        $this->previewType = 'cert';
        $this->previewModal = true;
    }

    public function previewTc(int $id): void
    {
        $this->previewId    = $id;
        $this->previewType  = 'tc';
        $this->previewModal = true;
    }

    public function closePreview(): void
    {
        $this->previewModal = false;
        $this->previewId    = null;
    }

    public function render()
    {
        $certificates = collect();
        $tcList       = collect();

        if ($this->activeTab === 'tc') {
            $q = TransferCertificate::with('student')
                ->where('organization_id', $this->organizationId);

            if ($this->search) {
                $q->where(function ($sq) {
                    $sq->where('tc_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas(
                            'student',
                            fn($s) =>
                            $s->where('full_name', 'like', '%' . $this->search . '%')
                                ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                        );
                });
            }
            $tcList = $q->orderByDesc('issue_date')->paginate($this->perPage);
        } else {
            $q = Certificate::with('student')
                ->where('organization_id', $this->organizationId)
                ->where('type', $this->activeTab);

            if ($this->search) {
                $q->where(function ($sq) {
                    $sq->where('event_name', 'like', '%' . $this->search . '%')
                        ->orWhere('certificate_no', 'like', '%' . $this->search . '%')
                        ->orWhereHas(
                            'student',
                            fn($s) =>
                            $s->where('full_name', 'like', '%' . $this->search . '%')
                                ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                        );
                });
            }
            $certificates = $q->orderByDesc('issued_date')->paginate($this->perPage);
        }

        // Preview data
        $previewCert = null;
        $previewTc   = null;

        if ($this->previewModal && $this->previewId) {
            if ($this->previewType === 'tc') {
                $previewTc = TransferCertificate::with(['student', 'organization'])->find($this->previewId);
            } else {
                $previewCert = Certificate::with(['student', 'organization'])->find($this->previewId);
            }
        }

        return view(
            'livewire.admin.tc-certificate',
            compact('certificates', 'tcList', 'previewCert', 'previewTc')
        );
    }
}
