<?php

namespace App\Livewire\Admin;

use App\Helpers\CityGetHelper;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Student extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    // ─── Edit state (holds user array during edit) ─────────────────────
    public $studentData = [];

    // ─── Form Fields ─────────────────────────────────────────────────────
    public $studentForm = [];
    public $dob           = '';
    public $studentsName  = '';
    public $studentsEmail = '';
    public $studentsMobile = '';
    public $studentsGender = '';
    public $studentsBoard  = '';
    public $studentsClass  = '';
    public $studentsSection = '';
    public $studentsActive  = 0;
    public $fatherName      = '';
    public $motherName      = '';
    public $religion        = '';
    public $localAddress    = '';
    public $permanentAddress = '';
    public $pincode          = '';
    public $aadharNo         = '';
    public $dateOfAdmission  = '';
    public $apparId          = null;
    public $registrationNumber = null;
    public $transportationRequired = false;

    // ─── Location ────────────────────────────────────────────────────────
    public $states        = [];
    public $cities        = [];
    public $selectedState = null;
    public $selectedCity  = null;

    // ─── Uploads ─────────────────────────────────────────────────────────
    public $studentImage     = null;
    public $studentImageUrl  = null;

    // ─── Modal ───────────────────────────────────────────────────────────
    public $open          = false;
    public $openImage     = false;
    public $showViewModal = false;
    public $editId        = null;
    public $viewModalTitle = '';
    public $viewData       = [];
    public $imagePath      = null;

    // ─── Standards / Sections ────────────────────────────────────────────
    public $standards       = [];
    public $sections        = [];
    public $filterSections  = [];

    // ─── Stats ───────────────────────────────────────────────────────────
    public $totalStudents      = 0;
    public $activeStudents     = 0;
    public $lastYearStudents   = 0;
    public $thisYearStudents   = 0;
    public $lastMonthAdmissions = 0;
    public $thisYearAdmissions  = 0;

    public string $search         = '';
    public string $filterClass    = '';
    public string $filterSection  = '';
    public string $filterGender   = '';
    public string $filterStatus   = '';
    public int    $perPage        = 50;

    protected $queryString = [
        'search'        => ['except' => ''],
        'filterClass'   => ['except' => ''],
        'filterSection' => ['except' => ''],
        'filterGender'  => ['except' => ''],
        'filterStatus'  => ['except' => ''],
    ];

    protected $listeners = [
        'onViewStudentAdmin',
        'onEditStudent',
        'onDeleteStudent',
        'onImageClick',
    ];

    public function mount(): void
    {
        $cityHelper        = new CityGetHelper();
        $this->states      = $cityHelper->getState();
        $this->standards   = Standard::where('organization_id', Auth::user()->organization_id)->get();

        $this->loadSections();
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $org = Auth::user()->organization_id;

        $base = fn() => StudentDetail::whereHas('user', fn($q) => $q->where('organization_id', $org));

        $this->totalStudents       = $base()->count();
        $this->activeStudents      = $base()->whereHas('user', fn($q) => $q->where('is_active', true))->count();
        $this->lastYearStudents    = $base()->whereYear('created_at', now()->subYear()->year)->count();
        $this->thisYearStudents    = $base()->whereYear('created_at', now()->year)->count();
        $this->lastMonthAdmissions = $base()->where('created_at', '>=', now()->subMonth())->count();
        $this->thisYearAdmissions  = $base()->whereYear('created_at', now()->year)->count();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterSection(): void
    {
        $this->resetPage();
    }

    public function updatedFilterGender(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClass(): void
    {
        $this->resetPage();
        $this->filterSection  = '';
        $this->filterSections = $this->filterClass
            ? Section::where('standard_id', $this->filterClass)->get()
            : [];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterClass', 'filterSection', 'filterGender', 'filterStatus']);
        $this->filterSections = [];
        $this->resetPage();
    }

    public function updatedSelectedState($value): void
    {
        $this->cities      = $value ? (new CityGetHelper())->cityGetByState($value) : [];
        $this->selectedCity = null;
    }

    public function loadSections(): void
    {
        $this->sections = $this->studentsClass
            ? Section::where('standard_id', $this->studentsClass)->get()
            : [];
    }

    public function updatedStudentsClass(): void
    {
        $this->loadSections();
    }

    public function onAddStudent(): void
    {
        $this->open = true;
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetForm();
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewData      = [];
        $this->viewModalTitle = '';
    }

    public function onImageClick($id): void
    {
        $this->imagePath = User::find($id)?->image;
        $this->openImage = true;
    }

    public function closeImage(): void
    {
        $this->openImage = false;
    }

    public function onSave(): void
    {
        $rules = [
            'studentsName'      => 'required|string|max:255',
            'studentsEmail'     => 'required|email|max:50',
            'studentsMobile'    => 'required|string|digits:10',
            'dob'               => 'required|date|before:today',
            'studentsGender'    => 'required|string|in:male,female,other',
            'studentsBoard'     => 'required|string',
            'studentsClass'     => 'nullable|integer|exists:standards,id',
            'studentsSection'   => 'nullable|integer|exists:sections,id',
            'fatherName'        => 'required|string|max:255',
            'motherName'        => 'required|string|max:255',
            'dateOfAdmission'   => 'required|date|before_or_equal:today',
            'aadharNo'          => 'nullable|digits:12',
            'pincode'           => 'nullable|digits:6',
            'studentImage'      => 'nullable|image|max:2048',
            'transportationRequired' => 'boolean',
            'apparId'           => 'nullable|string',
            'registrationNumber' => 'nullable|string',
        ];

        if (empty($this->studentData['id'])) {
            $rules['studentsEmail'] .= '|unique:users,email';
        }

        $this->validate($rules);

        try {
            $student = !empty($this->studentData['id']) ? User::find($this->studentData['id']) : new User();

            $studentData = [
                'name'            => $this->studentsName,
                'email'           => $this->studentsEmail,
                'mobile_number'   => $this->studentsMobile,
                'role'            => 'user',
                'is_active'       => $this->studentsActive ?? 0,
                'organization_id' => Auth::user()->organization_id,
            ];

            if ($this->studentImage) {
                if ($student->image) {
                    Storage::disk('s3')->delete(parse_url($student->image, PHP_URL_PATH));
                }
                $path = $this->studentImage->store('admin/students/images', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $studentData['image'] = Storage::disk('s3')->url($path);
            }

            $isNew = empty($this->studentData['id']);
            if ($isNew) {
                $plainPassword = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789@#$!'), 0, 10);
                $studentData['password'] = Hash::make($plainPassword);
            }

            $student->fill($studentData)->save();

            $detailData = [
                'user_id'                => $student->id,
                'standard_id'            => (int) $this->studentsClass,
                'section_id'             => (int) $this->studentsSection,
                'full_name'              => $this->studentsName,
                'father_name'            => $this->fatherName,
                'mother_name'            => $this->motherName,
                'email'                  => $this->studentsEmail,
                'dob'                    => $this->dob,
                'gender'                 => $this->studentsGender,
                'religion'               => $this->religion ?? null,
                'local_address'          => $this->localAddress ?? null,
                'permanent_address'      => $this->permanentAddress ?? null,
                'city'                   => $this->selectedCity ?? null,
                'state'                  => $this->selectedState ?? null,
                'pincode'                => $this->pincode ?? null,
                'admission_no'           => $this->generateAdmissionNumber(),
                'date_of_admission'      => $this->dateOfAdmission,
                'roll_no'                => $this->generateRollNumber(),
                'board'                  => $this->studentsBoard,
                'aadhar_no'              => $this->aadharNo ?? null,
                'phone'                  => $this->studentsMobile,
                'transportation_required' => $this->transportationRequired ?? false,
                'organization_id'        => Auth::user()->organization_id,
                'appar_id'               => $this->apparId ?? null,
                'registration_number'    => $this->registrationNumber ?? null,
            ];

            if (!empty($this->studentData['id'])) {
                StudentDetail::updateOrCreate(['user_id' => $student->id], $detailData);
                $this->notification()->success('Student Updated Successfully!');
            } else {
                StudentDetail::create($detailData);

                // Send password email on student creation
                try {
                    $templateKey = config('services.zeptomail.student_password_template_key');
                    if ($templateKey) {
                        $schoolName = Organization::find(Auth::user()->organization_id)?->name ?? 'School';
                        \App\Services\ZeptoMailService::sendTemplate(
                            $templateKey,
                            $student->email,
                            $student->name,
                            [
                                'password'         => $plainPassword,
                                'school_name'      => $schoolName,
                                'admission_number' => $detailData['admission_no'],
                                'username'         => $student->name,
                            ]
                        );
                    }
                } catch (\Throwable $e) {
                    logger()->error('Student password email failed: ' . $e->getMessage());
                }

                $this->notification()->success('Student Created Successfully!');
            }

            $this->resetForm();
            $this->loadStats();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->notification()->error('Error Saving Student', $e->getMessage());
            logger()->error('Student save error: ' . $e->getMessage());
        }
    }

    public function onViewStudentAdmin($id): void
    {
        $detail = StudentDetail::with(['user', 'standard', 'section'])->find($id);

        if (!$detail || !$detail->user) {
            $this->notification()->error('Student not found!');
            return;
        }

        $this->studentImageUrl = $detail->user->image;
        $this->viewModalTitle  = 'Student Details';
        $this->viewData        = ['user' => $detail->user, 'detail' => $detail];
        $this->showViewModal   = true;
    }

    public function onEditStudent($id): void
    {
        $detail = StudentDetail::find($id);
        if (!$detail) {
            abort(404);
        }

        $user = User::find($detail->user_id)?->refresh();
        if (!$user) {
            abort(404);
        }

        $this->studentData       = $user->toArray();
        $this->editId            = $user->id;
        $this->studentsName      = $user->name;
        $this->studentsEmail     = $user->email;
        $this->studentsMobile    = $user->mobile_number;
        $this->studentsActive    = $user->is_active;
        $this->dob               = $detail->dob;
        $this->fatherName        = $detail->father_name;
        $this->motherName        = $detail->mother_name;
        $this->studentsGender    = $detail->gender;
        $this->studentsBoard     = $detail->board;
        $this->studentsClass     = $detail->standard_id;
        $this->studentsSection   = $detail->section_id;
        $this->religion          = $detail->religion;
        $this->localAddress      = $detail->local_address;
        $this->permanentAddress  = $detail->permanent_address;
        $this->selectedState     = $detail->state;
        $this->selectedCity      = $detail->city;
        $this->pincode           = $detail->pincode;
        $this->aadharNo          = $detail->aadhar_no;
        $this->dateOfAdmission   = $detail->date_of_admission;
        $this->transportationRequired = $detail->transportation_required;
        $this->apparId           = $detail->appar_id;
        $this->registrationNumber = $detail->registration_number;
        $this->studentImageUrl   = $user->image;

        if ($this->selectedState) {
            $this->cities = (new CityGetHelper())->cityGetByState($this->selectedState);
        }

        $this->loadSections();
        $this->open = true;
    }

    public function onDeleteStudent($id): void
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
            $this->notification()->success('Student Deleted Successfully!');
            $this->loadStats();
            $this->resetPage();
        } else {
            $this->notification()->error('Student not found!');
        }
    }

    public function exportStudents(): StreamedResponse
    {
        $org      = Auth::user()->organization_id;
        $students = StudentDetail::with(['user', 'standard', 'section'])
            ->whereHas('user', fn($q) => $q->where('organization_id', $org))
            ->get();

        return response()->stream(function () use ($students) {
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
                'Status',
            ]);

            foreach ($students as $index => $s) {
                fputcsv($handle, [
                    $index + 1,
                    $s->full_name ?? '',
                    $s->user->email ?? '',
                    $s->phone ?? '',
                    ucfirst($s->gender ?? ''),
                    $s->dob?->format('d-m-Y') ?? '',
                    $s->religion ?? '',
                    $s->board ?? '',
                    $s->standard->name ?? '',
                    $s->section->name ?? '',
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
                    ($s->user->is_active ?? false) ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    protected function generateAdmissionNumber(): string
    {
        $year       = date('Y');
        $schoolCode = Auth::user()->organization->school_code;
        $class      = $this->studentsClass;
        $section    = $this->studentsSection;

        $last = StudentDetail::where('standard_id', $class)
            ->where('section_id', $section)
            ->where('admission_no', 'like', "$year$schoolCode$class$section%")
            ->orderBy('admission_no', 'desc')->first();

        $serial = $last ? (int) substr($last->admission_no, -4) + 1 : 1;
        return $year . $schoolCode . $class . $section . str_pad($serial, 4, '0', STR_PAD_LEFT);
    }

    protected function generateRollNumber(): string
    {
        $shortYear    = substr(date('Y'), -2);
        $schoolSerial = '01';
        $classFmt     = str_pad($this->studentsClass, 2, '0', STR_PAD_LEFT);
        $sectionCode  = substr($this->studentsSection, 0, 1);

        $last = StudentDetail::where('standard_id', $this->studentsClass)
            ->where('section_id', $this->studentsSection)
            ->where('roll_no', 'like', "$shortYear$schoolSerial$classFmt$sectionCode%")
            ->orderBy('roll_no', 'desc')->first();

        $serial = $last ? (int) substr($last->roll_no, -3) + 1 : 1;
        return $shortYear . $schoolSerial . $classFmt . $sectionCode . str_pad($serial, 3, '0', STR_PAD_LEFT);
    }

    protected function resetForm(): void
    {
        $this->reset([
            'studentData',
            'dob',
            'editId',
            'selectedState',
            'selectedCity',
            'studentsName',
            'studentsEmail',
            'studentsMobile',
            'studentsGender',
            'studentsBoard',
            'studentsClass',
            'studentsSection',
            'fatherName',
            'motherName',
            'religion',
            'localAddress',
            'permanentAddress',
            'pincode',
            'aadharNo',
            'dateOfAdmission',
            'studentImage',
            'studentImageUrl',
            'transportationRequired',
            'apparId',
            'registrationNumber',
            'studentsActive',
        ]);
        $this->open     = false;
        $this->sections = [];
    }

    public function render()
    {
        $students = StudentDetail::with(['user', 'standard', 'section'])
            ->whereHas('user', fn($q) => $q->where('organization_id', Auth::user()->organization_id))
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
            ->when($this->filterStatus !== '', fn($q) => $q->whereHas(
                'user',
                fn($q) => $q->where('is_active', $this->filterStatus)
            ))
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.student', compact('students'));
    }
}
