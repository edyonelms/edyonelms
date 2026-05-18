<?php

namespace App\Livewire\Accounts;

use App\Models\Admin\AdmissionEnquiry;
use App\Models\Admin\AdmissionExamPaper;
use App\Models\Student\Standard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Admissions extends Component
{
    use WireUiActions, WithPagination, WithFileUploads;

    // ─── Active tab ──────────────────────────────────────────────────────────────
    public $activeTab = 'admissions';

    // ─── Enquiry form fields ─────────────────────────────────────────────────────
    public $studentName = '';
    public $email = '';
    public $mobile = '';
    public $guardianName = '';
    public $address = '';
    public $standardId = '';
    public $stream = '';
    public $admissionFee = '';
    public $editEnquiryId = null;
    public $enquiryModalOpen = false;

    // ─── Update (result) form fields ─────────────────────────────────────────────
    public $updateEnquiryId = null;
    public $updateModalOpen = false;
    public $totalMarks = '';
    public $obtainedMarks = '';
    public $remarks = '';
    public $resultPdf = null;

    // ─── View modal ──────────────────────────────────────────────────────────────
    public $viewModalOpen = false;
    public $viewEnquiryData = [];

    // ─── Filters (admissions tab) ────────────────────────────────────────────────
    public $filterStandard = '';
    public $filterMonth    = '';
    public $search         = '';

    // ─── Exam Papers tab ─────────────────────────────────────────────────────────
    public $paperModalOpen   = false;   // upload modal
    public $editPaperModalOpen = false; // edit modal
    public $editPaperId      = null;
    public $paperStandardId  = '';
    public $paperTitle       = '';
    public $paperFile        = null;
    public $editPaperTitle   = '';
    public $editPaperStandardId = '';
    public $editPaperFile    = null;

    // ─── Exam paper filter ───────────────────────────────────────────────────────
    public $filterPaperStandard = '';

    // ─── Delete confirm ──────────────────────────────────────────────────────────
    public $pendingDeleteEnquiryId = null;
    public $pendingDeletePaperId   = null;

    public $perPage = 10;

    protected $queryString = [
        'activeTab'           => ['except' => 'admissions'],
        'filterStandard'      => ['except' => ''],
        'filterMonth'         => ['except' => ''],
        'search'              => ['except' => ''],
        'filterPaperStandard' => ['except' => ''],
    ];

    public function mount(): void
    {
        //
    }

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    // ─── Tab switching ───────────────────────────────────────────────────────────

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    //  ADMISSIONS TAB
    // ═══════════════════════════════════════════════════════════════════════════════

    // ─── Open / close enquiry modal ──────────────────────────────────────────────

    public function openEnquiryModal(?int $id = null): void
    {
        $this->resetEnquiryForm();
        $this->editEnquiryId = $id;
        $this->enquiryModalOpen = true;

        if ($id) {
            $e = AdmissionEnquiry::find($id);
            if (!$e) return;

            $this->studentName  = $e->student_name;
            $this->email        = $e->email;
            $this->mobile       = $e->mobile;
            $this->guardianName = $e->guardian_name;
            $this->address      = $e->address;
            $this->standardId   = $e->standard_id;
            $this->stream       = $e->stream;
            $this->admissionFee = $e->admission_fee;
        }
    }

    // ─── Save enquiry ────────────────────────────────────────────────────────────

    public function saveEnquiry(): void
    {
        $this->validate([
            'studentName'  => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'mobile'       => 'required|string|max:20',
            'guardianName' => 'required|string|max:255',
            'address'      => 'nullable|string|max:1000',
            'standardId'   => 'nullable|exists:standards,id',
            'stream'       => 'nullable|string|max:255',
            'admissionFee' => 'required|numeric|min:0',
        ]);

        try {
            $data = [
                'student_name'  => $this->studentName,
                'email'         => $this->email,
                'mobile'        => $this->mobile,
                'guardian_name' => $this->guardianName,
                'address'       => $this->address,
                'standard_id'   => $this->standardId ?: null,
                'stream'        => $this->stream ?: null,
                'admission_fee' => $this->admissionFee,
            ];

            if ($this->editEnquiryId) {
                $record = AdmissionEnquiry::where('id', $this->editEnquiryId)
                    ->where('organization_id', $this->orgId())
                    ->first();

                if ($record) {
                    $record->update($data);
                    $this->notification()->success('Enquiry updated successfully!');
                }
            } else {
                $data['organization_id'] = $this->orgId();
                $data['status'] = 'pending';
                AdmissionEnquiry::create($data);
                $this->notification()->success('Student enquiry added successfully!');
            }

            $this->resetEnquiryForm();
        } catch (\Exception $e) {
            $this->notification()->error('Error: ' . $e->getMessage());
        }
    }

    // ─── Edit enquiry ────────────────────────────────────────────────────────────

    public function editEnquiry(int $id): void
    {
        $this->openEnquiryModal($id);
    }

    // ─── View enquiry ────────────────────────────────────────────────────────────

    public function viewEnquiry(int $id): void
    {
        $enquiry = AdmissionEnquiry::with('standard')->find($id);
        if (!$enquiry) return;

        $this->viewEnquiryData = [
            'id'             => $enquiry->id,
            'student_name'   => $enquiry->student_name,
            'email'          => $enquiry->email ?? '-',
            'mobile'         => $enquiry->mobile,
            'guardian_name'  => $enquiry->guardian_name,
            'address'        => $enquiry->address ?? '-',
            'class'          => $enquiry->standard->name ?? '-',
            'stream'         => $enquiry->stream ?? '-',
            'admission_fee'  => $enquiry->admission_fee,
            'total_marks'    => $enquiry->total_marks,
            'obtained_marks' => $enquiry->obtained_marks,
            'remarks'        => $enquiry->remarks ?? '-',
            'result_pdf'     => $enquiry->result_pdf,
            'status'         => $enquiry->status,
            'created_at'     => $enquiry->created_at->format('d M Y'),
        ];
        $this->viewModalOpen = true;
    }

    // ─── Delete enquiry ──────────────────────────────────────────────────────────

    public function deleteEnquiry(int $id): void
    {
        $this->pendingDeleteEnquiryId = $id;
    }

    public function cancelDeleteEnquiry(): void
    {
        $this->pendingDeleteEnquiryId = null;
    }

    public function doDeleteEnquiry(): void
    {
        $enquiry = AdmissionEnquiry::where('id', $this->pendingDeleteEnquiryId)
            ->where('organization_id', $this->orgId())
            ->first();

        if ($enquiry) {
            if ($enquiry->result_pdf) {
                Storage::disk('s3')->delete($enquiry->result_pdf);
            }
            $enquiry->delete();
            $this->notification()->success('Enquiry deleted!');
        }
        $this->pendingDeleteEnquiryId = null;
    }

    // ─── Open update (result) modal ──────────────────────────────────────────────

    public function openUpdateModal(int $id): void
    {
        $this->resetUpdateForm();
        $this->updateEnquiryId = $id;
        $this->updateModalOpen = true;

        $enquiry = AdmissionEnquiry::find($id);
        if ($enquiry) {
            $this->totalMarks    = $enquiry->total_marks ?? '';
            $this->obtainedMarks = $enquiry->obtained_marks ?? '';
            $this->remarks       = $enquiry->remarks ?? '';
        }
    }

    // ─── Save update (result) ────────────────────────────────────────────────────

    public function saveUpdate(): void
    {
        $this->validate([
            'totalMarks'    => 'required|numeric|min:0',
            'obtainedMarks' => 'required|numeric|min:0',
            'remarks'       => 'nullable|string|max:2000',
            'resultPdf'     => 'nullable|file|mimes:pdf|max:10240',
        ]);

        try {
            $enquiry = AdmissionEnquiry::where('id', $this->updateEnquiryId)
                ->where('organization_id', $this->orgId())
                ->first();

            if (!$enquiry) return;

            $updateData = [
                'total_marks'    => $this->totalMarks,
                'obtained_marks' => $this->obtainedMarks,
                'remarks'        => $this->remarks ?: null,
                'status'         => 'updated',
            ];

            if ($this->resultPdf) {
                if ($enquiry->result_pdf) {
                    Storage::disk('s3')->delete($enquiry->result_pdf);
                }
                $path = $this->resultPdf->store(
                    'accounts/admissions/result-pdfs/' . $this->orgId(),
                    's3'
                );
                $updateData['result_pdf'] = $path;
            }

            $enquiry->update($updateData);
            $this->notification()->success('Student result updated successfully!');
            $this->resetUpdateForm();
        } catch (\Exception $e) {
            $this->notification()->error('Error: ' . $e->getMessage());
        }
    }

    // ─── Reset helpers ───────────────────────────────────────────────────────────

    private function resetEnquiryForm(): void
    {
        $this->reset([
            'editEnquiryId', 'enquiryModalOpen',
            'studentName', 'email', 'mobile', 'guardianName',
            'address', 'standardId', 'stream', 'admissionFee',
        ]);
    }

    private function resetUpdateForm(): void
    {
        $this->reset([
            'updateEnquiryId', 'updateModalOpen',
            'totalMarks', 'obtainedMarks', 'remarks', 'resultPdf',
        ]);
    }

    // ─── Filter watchers ─────────────────────────────────────────────────────────

    public function updatedFilterStandard(): void
    {
        $this->resetPage();
    }

    public function updatedFilterMonth(): void
    {
        $this->resetPage();
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    //  EXAM PAPERS TAB
    // ═══════════════════════════════════════════════════════════════════════════════

    // ─── Open paper upload modal ─────────────────────────────────────────────────
    public function openPaperModal(): void
    {
        $this->reset(['paperStandardId', 'paperTitle', 'paperFile']);
        $this->paperModalOpen = true;
    }

    public function closePaperModal(): void
    {
        $this->paperModalOpen = false;
        $this->reset(['paperStandardId', 'paperTitle', 'paperFile']);
    }

    // ─── Open edit paper modal ───────────────────────────────────────────────────
    public function openEditPaperModal(int $id): void
    {
        $paper = AdmissionExamPaper::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->firstOrFail();

        $this->editPaperId         = $id;
        $this->editPaperTitle      = $paper->title ?? '';
        $this->editPaperStandardId = $paper->standard_id;
        $this->editPaperFile       = null;
        $this->editPaperModalOpen  = true;
    }

    public function closeEditPaperModal(): void
    {
        $this->editPaperModalOpen = false;
        $this->reset(['editPaperId', 'editPaperTitle', 'editPaperStandardId', 'editPaperFile']);
    }

    public function saveEditPaper(): void
    {
        $this->validate([
            'editPaperStandardId' => 'required|exists:standards,id',
            'editPaperTitle'      => 'nullable|string|max:255',
            'editPaperFile'       => 'nullable|file|mimes:pdf|max:1024',  // 1 MB
        ]);

        try {
            $paper = AdmissionExamPaper::where('id', $this->editPaperId)
                ->where('organization_id', $this->orgId())
                ->firstOrFail();

            $updateData = [
                'standard_id' => $this->editPaperStandardId,
                'title'       => $this->editPaperTitle ?: null,
            ];

            if ($this->editPaperFile) {
                Storage::disk('s3')->delete($paper->file_path);
                $updateData['file_path'] = $this->editPaperFile->store(
                    'accounts/admissions/exam-papers/' . $this->orgId(), 's3'
                );
            }

            $paper->update($updateData);
            $this->notification()->success('Paper updated successfully!');
            $this->closeEditPaperModal();
        } catch (\Exception $e) {
            $this->notification()->error('Error: ' . $e->getMessage());
        }
    }

    public function saveExamPaper(): void
    {
        $this->validate([
            'paperStandardId' => 'required|exists:standards,id',
            'paperTitle'      => 'nullable|string|max:255',
            'paperFile'       => 'required|file|mimes:pdf|max:1024',  // 1 MB
        ]);

        try {
            $path = $this->paperFile->store(
                'admission-exam-papers/' . $this->orgId(),
                's3'
            );

            AdmissionExamPaper::create([
                'organization_id' => $this->orgId(),
                'standard_id'     => $this->paperStandardId,
                'title'           => $this->paperTitle ?: null,
                'file_path'       => $path,
            ]);

            $this->notification()->success('Exam paper uploaded successfully!');
            $this->closePaperModal();
        } catch (\Exception $e) {
            $this->notification()->error('Error: ' . $e->getMessage());
        }
    }

    public function deleteExamPaper(int $id): void
    {
        $this->pendingDeletePaperId = $id;
    }

    public function cancelDeletePaper(): void
    {
        $this->pendingDeletePaperId = null;
    }

    public function doDeleteExamPaper(): void
    {
        $paper = AdmissionExamPaper::where('id', $this->pendingDeletePaperId)
            ->where('organization_id', $this->orgId())
            ->first();

        if ($paper) {
            Storage::disk('s3')->delete($paper->file_path);
            $paper->delete();
            $this->notification()->success('Exam paper deleted!');
        }
        $this->pendingDeletePaperId = null;
    }

    public function downloadExamPaper(int $id): mixed
    {
        $paper = AdmissionExamPaper::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->first();

        if (!$paper) {
            $this->notification()->error('Paper not found.');
            return null;
        }

        // Force download via temporary signed URL with Content-Disposition header
        $url = Storage::disk('s3')->temporaryUrl(
            $paper->file_path,
            now()->addMinutes(5),
            ['ResponseContentDisposition' => 'attachment; filename="' . ($paper->title ?? 'exam-paper') . '.pdf"']
        );
        return $this->redirect($url);
    }

    public function downloadResultPdf(int $id): mixed
    {
        $enquiry = AdmissionEnquiry::where('id', $id)
            ->where('organization_id', $this->orgId())
            ->first();

        if (!$enquiry || !$enquiry->result_pdf) {
            $this->notification()->error('PDF not found.');
            return null;
        }

        $url = Storage::disk('s3')->temporaryUrl(
            $enquiry->result_pdf,
            now()->addMinutes(5),
            ['ResponseContentDisposition' => 'attachment; filename="result-' . $enquiry->student_name . '.pdf"']
        );
        return $this->redirect($url);
    }

    // ─── Render ──────────────────────────────────────────────────────────────────

    public function render()
    {
        $orgId = $this->orgId();

        $standards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)->orderBy('order')->get();

        // ── Analytics ────────────────────────────────────────────────────────────
        $totalStudents = AdmissionEnquiry::where('organization_id', $orgId)->count();
        $updatedStudents = AdmissionEnquiry::where('organization_id', $orgId)->where('status', 'updated')->count();
        $thisMonthAdded = AdmissionEnquiry::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $lastMonthAdded = AdmissionEnquiry::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        // ── Enquiries list ───────────────────────────────────────────────────────
        $enquiries = AdmissionEnquiry::with('standard')
            ->where('organization_id', $orgId)
            ->when($this->search, fn($q) => $q->where(fn($s) =>
                $s->where('student_name', 'like', "%{$this->search}%")
                  ->orWhere('mobile', 'like', "%{$this->search}%")
            ))
            ->when($this->filterStandard, fn($q) => $q->where('standard_id', $this->filterStandard))
            ->when($this->filterMonth, function ($q) {
                $parts = explode('-', $this->filterMonth);
                if (count($parts) === 2) {
                    $q->whereYear('created_at', $parts[0])
                      ->whereMonth('created_at', $parts[1]);
                }
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        // ── Exam papers list ─────────────────────────────────────────────────────
        $examPapers = AdmissionExamPaper::with('standard')
            ->where('organization_id', $orgId)
            ->when($this->filterPaperStandard, fn($q) => $q->where('standard_id', $this->filterPaperStandard))
            ->orderByDesc('created_at')
            ->get();

        // ── Month options for filter ─────────────────────────────────────────────
        $monthOptions = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthOptions[] = [
                'value' => $date->format('Y-m'),
                'label' => $date->format('F Y'),
            ];
        }

        return view('livewire.accounts.admissions', [
            'standards'       => $standards,
            'enquiries'       => $enquiries,
            'examPapers'      => $examPapers,
            'totalStudents'   => $totalStudents,
            'updatedStudents' => $updatedStudents,
            'thisMonthAdded'  => $thisMonthAdded,
            'lastMonthAdded'  => $lastMonthAdded,
            'monthOptions'    => $monthOptions,
        ]);
    }
}
