<?php

namespace App\Livewire\SuperAdmin;

use App\Helpers\CityGetHelper;
use App\Models\Admin\SchoolInfo;
use App\Models\Organization;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use App\Models\User;
use App\Services\ZeptoMailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Student extends Component
{
    use WireUiActions, WithPagination;

    // ─── Stats ───────────────────────────────────────────────────────────────
    public int $totalSchools     = 0;
    public int $totalStudents    = 0;
    public int $activeStudents   = 0;
    public int $inactiveStudents = 0;

    // ─── View Modal ──────────────────────────────────────────────────────────
    public bool   $showViewModal  = false;
    public string $viewModalTitle = '';
    public array  $viewData       = [];
    public        $studentImageUrl = null;

    // ─── Filters ─────────────────────────────────────────────────────────────
    public string $search             = '';
    public string $filterOrganization = '';
    public string $filterClass        = '';
    public string $filterSection      = '';
    public string $filterGender       = '';
    public string $filterStatus       = '';
    public int    $perPage            = 50;

    // ─── Filter Options ───────────────────────────────────────────────────────
    public $organizations  = [];
    public $standards      = [];
    public $filterSections = [];

    protected $queryString = [
        'search'             => ['except' => ''],
        'filterOrganization' => ['except' => ''],
        'filterClass'        => ['except' => ''],
        'filterSection'      => ['except' => ''],
        'filterGender'       => ['except' => ''],
        'filterStatus'       => ['except' => ''],
    ];

    // ─── Add Student Panel ────────────────────────────────────────────────────
    public bool   $showAddPanel        = false;
    public string $addOrgId            = '';
    public string $addName             = '';
    public string $addEmail            = '';
    public string $addMobile           = '';
    public string $addGender           = '';
    public string $addDob              = '';
    public string $addFatherName       = '';
    public string $addMotherName       = '';
    public string $addReligion         = '';
    public string $addLocalAddress     = '';
    public string $addPermanentAddress = '';
    public string $addState            = '';
    public string $addCity             = '';
    public string $addPincode          = '';
    public string $addAadharNo         = '';
    public string $addBoard            = '';
    public string $addDateOfAdmission  = '';
    public string $addStandardId       = '';
    public string $addSectionId        = '';
    public bool   $addTransportation   = false;
    public string $addApparId          = '';
    public string $addRegNo            = '';
    public        $addStandards        = [];
    public        $addSections         = [];
    public        $addStates           = [];
    public        $addCities           = [];

    protected $listeners = ['onViewStudentSuperAdmin', 'onDeleteStudentSuperAdmin'];

    // ─── Mount ────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->organizations = Organization::orderBy('name')->get();
        $this->loadStats();
    }

    // ─── Base Query (shared by stats, table, export) ──────────────────────────

    private function baseQuery()
    {
        return StudentDetail::with(['user', 'standard', 'section', 'user.organization'])
            ->when($this->filterOrganization, fn($q) => $q->whereHas('user', fn($q) => $q->where('organization_id', $this->filterOrganization)))
            ->when($this->search, fn($q) => $q->where(
                fn($q) => $q
                    ->where('full_name', 'like', "%{$this->search}%")
                    ->orWhere('admission_no', 'like', "%{$this->search}%")
                    ->orWhere('roll_no', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn($q) => $q->where('email', 'like', "%{$this->search}%"))
            ))
            ->when($this->filterClass,   fn($q) => $q->where('standard_id', $this->filterClass))
            ->when($this->filterSection, fn($q) => $q->where('section_id', $this->filterSection))
            ->when($this->filterGender,  fn($q) => $q->where('gender', $this->filterGender))
            ->when($this->filterStatus !== '', fn($q) => $q->whereHas('user', fn($q) => $q->where('is_active', $this->filterStatus)));
    }

    // ─── Stats ────────────────────────────────────────────────────────────────

    private function loadStats(): void
    {
        $this->totalSchools     = SchoolInfo::count();
        $this->totalStudents    = $this->baseQuery()->count();
        $this->activeStudents   = $this->baseQuery()->whereHas('user', fn($q) => $q->where('is_active', 1))->count();
        $this->inactiveStudents = $this->baseQuery()->whereHas('user', fn($q) => $q->where('is_active', 0))->count();
    }

    // ─── Filter Hooks ─────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFilterOrganization(): void
    {
        $this->resetPage();
        $this->filterClass   = '';
        $this->filterSection = '';

        $this->standards = $this->filterOrganization
            ? Standard::where('organization_id', $this->filterOrganization)->get()
            : [];
        $this->filterSections = [];
        $this->loadStats();
    }

    public function updatedFilterClass(): void
    {
        $this->resetPage();
        $this->filterSection  = '';
        $this->filterSections = $this->filterClass
            ? Section::where('standard_id', $this->filterClass)->get()
            : [];
        $this->loadStats();
    }

    public function updatedFilterSection(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFilterGender(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterOrganization', 'filterClass', 'filterSection', 'filterGender', 'filterStatus']);
        $this->standards      = [];
        $this->filterSections = [];
        $this->resetPage();
        $this->loadStats();
    }

    // ─── View ─────────────────────────────────────────────────────────────────

    public function onViewStudentSuperAdmin($id): void
    {
        $detail = StudentDetail::with(['user', 'standard', 'section', 'user.organization.schoolInfo'])
            ->where('user_id', $id)
            ->first();

        if (!$detail) {
            $this->notification()->error('Student not found!');
            return;
        }

        $this->studentImageUrl = $detail->user?->image;
        $this->viewModalTitle  = 'Student Details';
        $this->viewData        = [
            'user'     => $detail->user,
            'detail'   => $detail,
            'standard' => $detail->standard,
            'section'  => $detail->section,
        ];
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal  = false;
        $this->viewData       = [];
        $this->viewModalTitle = '';
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function onDeleteStudentSuperAdmin($id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'icon'        => 'exclamation-circle',
            'iconColor'   => 'text-red-500',
            'description' => 'Are you sure you want to delete this student? This action cannot be undone.',
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'doDeleteStudent',
                'params' => $id,
                'color'  => 'negative',
            ],
            'reject' => ['label' => 'No'],
        ]);
    }

    public function doDeleteStudent($id): void
    {
        $detail = StudentDetail::find($id);
        if ($detail) {
            $user = User::find($detail->user_id);
            $detail->delete();
            $user?->delete();
            $this->loadStats();
            $this->resetPage();
            $this->notification()->success('Student deleted successfully!');
        } else {
            $this->notification()->error('Student not found!');
        }
    }

    public function exportStudents()
    {
        if (!$this->filterOrganization) return;

        $students = $this->baseQuery()->get();

        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'S.No',
                'Full Name',
                'Email',
                'Mobile',
                'Gender',
                'Date of Birth',
                'Religion',
                'Board',
                'Class',
                'Section',
                'Roll No',
                'Admission No',
                'Date of Admission',
                'Father Name',
                'Mother Name',
                'Aadhar No',
                'Appar ID',
                'Registration Number',
                'Local Address',
                'Permanent Address',
                'City',
                'State',
                'Pincode',
                'Transportation Required',
                'School',
                'Status',
            ]);

            foreach ($students as $index => $s) {
                fputcsv($handle, [
                    $index + 1,
                    $s->full_name ?? '',
                    $s->user?->email ?? '',
                    $s->phone ?? '',
                    ucfirst($s->gender ?? ''),
                    $s->dob?->format('d-m-Y') ?? '',
                    $s->religion ?? '',
                    $s->board ?? '',
                    $s->standard?->name ?? '',
                    $s->section?->name ?? '',
                    $s->roll_no ?? '',
                    $s->admission_no ?? '',
                    $s->date_of_admission?->format('d-m-Y') ?? '',
                    $s->father_name ?? '',
                    $s->mother_name ?? '',
                    $s->aadhar_no ?? '',
                    $s->appar_id ?? '',
                    $s->registration_number ?? '',
                    $s->local_address ?? '',
                    $s->permanent_address ?? '',
                    $s->city ?? '',
                    $s->state ?? '',
                    $s->pincode ?? '',
                    $s->transportation_required ? 'Yes' : 'No',
                    $s->user?->organization?->name ?? '',
                    ($s->user?->is_active) ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 'students_' . now()->format('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    // ─── Add Student Panel ────────────────────────────────────────────────────

    public function openAddPanel(): void
    {
        $this->addStates          = (new CityGetHelper())->getState();
        $this->addOrgId           = '';
        $this->addName            = '';
        $this->addEmail           = '';
        $this->addMobile          = '';
        $this->addGender          = '';
        $this->addDob             = '';
        $this->addFatherName      = '';
        $this->addMotherName      = '';
        $this->addReligion        = '';
        $this->addLocalAddress    = '';
        $this->addPermanentAddress = '';
        $this->addState           = '';
        $this->addCity            = '';
        $this->addPincode         = '';
        $this->addAadharNo        = '';
        $this->addBoard           = '';
        $this->addDateOfAdmission = now()->format('Y-m-d');
        $this->addStandardId      = '';
        $this->addSectionId       = '';
        $this->addTransportation  = false;
        $this->addApparId         = '';
        $this->addRegNo           = '';
        $this->addStandards       = [];
        $this->addSections        = [];
        $this->addCities          = [];
        $this->resetValidation();
        $this->showAddPanel       = true;
    }

    public function closeAddPanel(): void
    {
        $this->showAddPanel = false;
        $this->resetValidation();
    }

    public function updatedAddOrgId(): void
    {
        $this->addStandardId = '';
        $this->addSectionId  = '';
        $this->addSections   = [];
        $this->addBoard      = '';

        if ($this->addOrgId) {
            $this->addStandards = Standard::where('organization_id', $this->addOrgId)->orderBy('name')->get();
            $org = Organization::find($this->addOrgId);
            $this->addBoard = $org?->education_board ?? '';
        } else {
            $this->addStandards = [];
        }
    }

    public function updatedAddStandardId(): void
    {
        $this->addSectionId = '';
        $this->addSections  = $this->addStandardId
            ? Section::where('standard_id', $this->addStandardId)->get()
            : [];
    }

    public function updatedAddState(): void
    {
        $this->addCity   = '';
        $this->addCities = $this->addState
            ? (new CityGetHelper())->cityGetByState($this->addState)
            : [];
    }

    public function saveNewStudent(): void
    {
        $this->validate([
            'addOrgId'           => 'required|integer|exists:organizations,id',
            'addName'            => 'required|string|max:255',
            'addEmail'           => 'required|email|max:100|unique:users,email',
            'addMobile'          => 'required|digits:10',
            'addGender'          => 'required|in:male,female,other',
            'addDob'             => 'required|date|before:today',
            'addFatherName'      => 'required|string|max:255',
            'addMotherName'      => 'required|string|max:255',
            'addBoard'           => 'required|string|max:100',
            'addDateOfAdmission' => 'required|date|before_or_equal:today',
            'addReligion'        => 'nullable|string|max:100',
            'addLocalAddress'    => 'nullable|string|max:500',
            'addPermanentAddress'=> 'nullable|string|max:500',
            'addPincode'         => 'nullable|digits:6',
            'addAadharNo'        => 'nullable|digits:12',
            'addStandardId'      => 'nullable|integer|exists:standards,id',
            'addSectionId'       => 'nullable|integer|exists:sections,id',
        ]);

        $org           = Organization::findOrFail($this->addOrgId);
        $plainPassword = Str::upper(Str::random(4)) . rand(100, 999) . Str::random(3);

        $user = User::create([
            'name'            => $this->addName,
            'email'           => $this->addEmail,
            'mobile_number'   => $this->addMobile,
            'role'            => 'user',
            'is_active'       => true,
            'organization_id' => $this->addOrgId,
            'password'        => Hash::make($plainPassword),
        ]);

        $admissionNo = $this->generateAdmissionNo($org, $this->addStandardId, $this->addSectionId);
        $rollNo      = $this->generateRollNo($this->addStandardId, $this->addSectionId);

        StudentDetail::create([
            'user_id'                => $user->id,
            'organization_id'        => $this->addOrgId,
            'standard_id'            => $this->addStandardId ?: null,
            'section_id'             => $this->addSectionId ?: null,
            'full_name'              => $this->addName,
            'father_name'            => $this->addFatherName,
            'mother_name'            => $this->addMotherName,
            'email'                  => $this->addEmail,
            'dob'                    => $this->addDob,
            'gender'                 => $this->addGender,
            'religion'               => $this->addReligion ?: null,
            'phone'                  => $this->addMobile,
            'local_address'          => $this->addLocalAddress ?: null,
            'permanent_address'      => $this->addPermanentAddress ?: null,
            'state'                  => $this->addState ?: null,
            'city'                   => $this->addCity ?: null,
            'pincode'                => $this->addPincode ?: null,
            'aadhar_no'              => $this->addAadharNo ?: null,
            'board'                  => $this->addBoard,
            'admission_no'           => $admissionNo,
            'date_of_admission'      => $this->addDateOfAdmission,
            'roll_no'                => $rollNo,
            'transportation_required'=> $this->addTransportation,
            'appar_id'               => $this->addApparId ?: null,
            'registration_number'    => $this->addRegNo ?: null,
        ]);

        try {
            $templateKey = config('services.zeptomail.student_password_template_key');
            if ($templateKey) {
                ZeptoMailService::sendTemplate($templateKey, $this->addEmail, $this->addName, [
                    'password'         => $plainPassword,
                    'school_name'      => $org->name,
                    'admission_number' => $admissionNo,
                    'username'         => $this->addName,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Student welcome email failed', ['email' => $this->addEmail, 'error' => $e->getMessage()]);
        }

        $this->closeAddPanel();
        $this->loadStats();
        $this->resetPage();
        $this->notification()->success('Student Added!', $this->addName . ' added to ' . $org->name . '.');
    }

    private function generateAdmissionNo(Organization $org, string $standardId, string $sectionId): string
    {
        $year       = date('Y');
        $schoolCode = $org->school_code ?? 'SCH';
        $cls        = $standardId ?: '0';
        $sec        = $sectionId ?: '0';

        $last = StudentDetail::where('organization_id', $org->id)
            ->where('admission_no', 'like', "$year$schoolCode$cls$sec%")
            ->orderBy('admission_no', 'desc')
            ->first();

        $serial = $last ? (int) substr($last->admission_no, -4) + 1 : 1;
        return $year . $schoolCode . $cls . $sec . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    private function generateRollNo(string $standardId, string $sectionId): string
    {
        $shortYear    = substr(date('Y'), -2);
        $schoolSerial = '01';
        $classFmt     = str_pad($standardId ?: '0', 2, '0', STR_PAD_LEFT);
        $sectionCode  = $sectionId ? substr($sectionId, 0, 1) : '0';

        $last = StudentDetail::where('organization_id', $this->addOrgId)
            ->where('standard_id', $standardId ?: null)
            ->where('section_id', $sectionId ?: null)
            ->where('roll_no', 'like', "$shortYear$schoolSerial$classFmt$sectionCode%")
            ->orderBy('roll_no', 'desc')
            ->first();

        $serial = $last ? (int) substr($last->roll_no, -3) + 1 : 1;
        return $shortYear . $schoolSerial . $classFmt . $sectionCode . str_pad($serial, 3, '0', STR_PAD_LEFT);
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $students = $this->baseQuery()->latest()->paginate($this->perPage);
        return view('livewire.super-admin.student', compact('students'));
    }
}
