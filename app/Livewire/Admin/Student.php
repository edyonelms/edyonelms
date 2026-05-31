<?php

namespace App\Livewire\Admin;

use App\Helpers\CityGetHelper;
use App\Models\Admin\Transportation;
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
    // Board is no longer entered on the form — it's auto-derived from the
    // selected class (Standard::board). Kept here so old listeners/views
    // referencing it don't 500; cleared on form reset.
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
    public $transportationRequired = '0'; // '1' = Yes, '0' = No (string keeps Livewire select binding reliable)

    // ─── Transport route selection ───────────────────────────────────────
    public $selectedRoute = null;
    public $routeOptions  = [];

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

    // ─── Custom delete overlay (replaces broken WireUI dialog) ──────────
    public bool $showDeleteConfirm = false;
    public $deleteTargetId         = null;

    public string $search         = '';
    public string $filterClass    = '';
    public string $filterSection  = '';
    public string $filterGender   = '';
    public string $filterStatus   = '';
    /** Sort key — name_asc (A→Z), admission_no (asc), roll_no (asc). */
    public string $sortBy         = 'name_asc';
    public int    $perPage        = 50;

    protected $queryString = [
        'search'        => ['except' => ''],
        'filterClass'   => ['except' => ''],
        'filterSection' => ['except' => ''],
        'filterGender'  => ['except' => ''],
        'filterStatus'  => ['except' => ''],
        'sortBy'        => ['except' => 'name_asc'],
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

        $this->loadRoutes();
        $this->loadSections();
        $this->loadStats();
    }

    private function loadRoutes(): void
    {
        $this->routeOptions = Transportation::where('organization_id', Auth::user()->organization_id)
            ->where('is_active', true)
            ->orderBy('route_name')
            ->get(['id', 'route_name', 'monthly_fee']);
    }

    public function updatedTransportationRequired($value): void
    {
        // Clear any selected route when transport is turned off
        if (!$this->transportationRequired) {
            $this->selectedRoute = null;
        }
    }

    /**
     * Attach the chosen transport route to the student (or detach all when
     * transport is not required). Assigning the route makes the route's fee
     * flow into the Transport module's student list & fee summary.
     */
    private function syncTransportRoute(StudentDetail $detail): void
    {
        if ($this->transportationRequired && $this->selectedRoute) {
            $detail->transportations()->sync([
                (int) $this->selectedRoute => ['organization_id' => Auth::user()->organization_id],
            ]);
        } else {
            $detail->transportations()->detach();
        }
    }

    private function loadStats(): void
    {
        $org = Auth::user()->organization_id;

        // Single aggregate query — was 6 separate COUNT(*) queries with
        // expensive whereHas('user') subqueries each time. StudentDetail
        // has its own organization_id column, so use it directly.
        $stats = StudentDetail::where('organization_id', $org)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN YEAR(created_at) = ? THEN 1 ELSE 0 END) as this_year,
                SUM(CASE WHEN YEAR(created_at) = ? THEN 1 ELSE 0 END) as last_year,
                SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as last_month
            ', [now()->year, now()->subYear()->year, now()->subMonth()])
            ->first();

        $this->totalStudents       = (int) ($stats->total ?? 0);
        $this->thisYearStudents    = (int) ($stats->this_year ?? 0);
        $this->lastYearStudents    = (int) ($stats->last_year ?? 0);
        $this->lastMonthAdmissions = (int) ($stats->last_month ?? 0);
        $this->thisYearAdmissions  = $this->thisYearStudents;

        // Active count needs to join users for is_active — separate query
        $this->activeStudents = StudentDetail::where('organization_id', $org)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->count();
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

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterClass', 'filterSection', 'filterGender', 'filterStatus']);
        $this->sortBy         = 'name_asc';
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

    public function getIsOrphanedProperty(): bool
    {
        // Student was orphaned when their previously-assigned class got deleted.
        // We only flag the *edit* form: a brand-new student doesn't have an id yet.
        return !empty($this->studentData['id']) && empty($this->studentsClass);
    }

    public function onSave(): void
    {
        // Orphaned students cannot be activated until a class is re-assigned (req #6).
        if ($this->isOrphaned && (int) $this->studentsActive === 1) {
            $this->addError('studentsActive', 'Assign a class before activating this student.');
            return;
        }

        $rules = [
            'studentsName'      => 'required|string|max:255',
            'studentsEmail'     => 'required|email|max:50',
            'studentsMobile'    => 'required|string|digits:10',
            'dob'               => 'required|date|before:today',
            'studentsGender'    => 'required|string|in:male,female,other',
            // Board is auto-fetched from the selected class — not a form field.
            'studentsClass'     => 'required|integer|exists:standards,id',
            'studentsSection'   => 'required|integer|exists:sections,id',
            'fatherName'        => 'required|string|max:255',
            'motherName'        => 'nullable|string|max:255',
            'dateOfAdmission'   => 'nullable|date|before_or_equal:today',
            'aadharNo'          => 'nullable|digits:12',
            'pincode'           => 'nullable|digits:6',
            'studentImage'      => 'nullable|image|max:2048',
            'transportationRequired' => 'boolean',
            'selectedRoute'     => $this->transportationRequired
                ? 'required|integer|exists:transportations,id'
                : 'nullable',
            'apparId'           => 'nullable|string',
            'registrationNumber' => 'nullable|string',
        ];

        $messages = [
            'studentsClass.required'   => 'Please select a class.',
            'studentsSection.required' => 'Please select a section.',
            'selectedRoute.required'   => 'Please select a transport route.',
        ];

        if (empty($this->studentData['id'])) {
            $rules['studentsEmail'] .= '|unique:users,email';
        }

        $this->validate($rules, $messages);

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

            $isNewStudent = empty($this->studentData['id']);

            // Generate identifiers ONCE so the list, DB and email always match.
            // On edit, keep the existing admission/roll numbers.
            if ($isNewStudent) {
                $admissionNo = $this->generateAdmissionNumber();
                $rollNo      = $this->generateRollNumber();
            } else {
                $existingDetail = StudentDetail::where('user_id', $student->id)->first(['admission_no', 'roll_no']);
                $admissionNo = $existingDetail->admission_no ?? $this->generateAdmissionNumber();
                $rollNo      = $existingDetail->roll_no ?? $this->generateRollNumber();
            }

            // Board is auto-fetched from the chosen class (no form field).
            $standardBoard = Standard::where('id', (int) $this->studentsClass)->value('board')
                ?? Auth::user()->organization->education_board
                ?? null;

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
                'admission_no'           => $admissionNo,
                'date_of_admission'      => $this->dateOfAdmission ?: now()->toDateString(),
                'roll_no'                => $rollNo,
                'board'                  => $standardBoard,
                'aadhar_no'              => $this->aadharNo ?? null,
                'phone'                  => $this->studentsMobile,
                'transportation_required' => (bool) $this->transportationRequired,
                'organization_id'        => Auth::user()->organization_id,
                'appar_id'               => $this->apparId ?? null,
                'registration_number'    => $this->registrationNumber ?? null,
            ];

            if (!empty($this->studentData['id'])) {
                $detail = StudentDetail::updateOrCreate(['user_id' => $student->id], $detailData);
                $this->syncTransportRoute($detail);
                $this->notification()->success('Student Updated Successfully!');
            } else {
                $detail = StudentDetail::create($detailData);
                $this->syncTransportRoute($detail);

                // Send welcome email with login credentials on student creation
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
                                'admission_number' => $admissionNo,
                                'username'         => $student->name,
                                'name'             => $student->name,
                                'email'            => $student->email,
                                'login_url'        => url('/login'),
                            ]
                        );
                        logger()->info('Student welcome email sent to: ' . $student->email);
                    } else {
                        logger()->warning('ZEPTOMAIL_STUDENT_PASSWORD_TEMPLATE_KEY not configured — skipping welcome email.');
                    }
                } catch (\Throwable $e) {
                    logger()->error('Student welcome email failed for ' . $student->email . ': ' . $e->getMessage());
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
        $detail = StudentDetail::with(['user', 'standard', 'section', 'transportations', 'organization'])->find($id);

        if (!$detail || !$detail->user) {
            $this->notification()->error('Student not found!');
            return;
        }

        $this->studentImageUrl = $detail->user->image;
        $this->viewModalTitle  = 'Student Details';
        $this->viewData        = [
            'user'         => $detail->user,
            'detail'       => $detail,
            'organization' => $detail->organization,
        ];
        $this->showViewModal   = true;
    }

    public function onEditStudent($id): void
    {
        // Close the view panel first so the edit slide-in doesn't stack on top.
        $this->showViewModal = false;
        $this->viewData      = [];

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
        $this->studentsName      = (string) ($user->name ?? '');
        $this->studentsEmail     = (string) ($user->email ?? '');
        $this->studentsMobile    = (string) ($user->mobile_number ?? '');
        $this->studentsActive    = (int) ($user->is_active ?? 0);
        // Cast Carbon dates to Y-m-d strings — Livewire chokes on Carbon
        // instances in public properties and can 500 the request
        $this->dob               = $detail->dob ? $detail->dob->format('Y-m-d') : '';
        $this->dateOfAdmission   = $detail->date_of_admission ? $detail->date_of_admission->format('Y-m-d') : '';
        $this->fatherName        = (string) ($detail->father_name ?? '');
        $this->motherName        = (string) ($detail->mother_name ?? '');
        $this->studentsGender    = (string) ($detail->gender ?? '');
        $this->studentsBoard     = (string) ($detail->board ?? '');
        $this->studentsClass     = (string) ($detail->standard_id ?? '');
        $this->studentsSection   = (string) ($detail->section_id ?? '');
        $this->religion          = (string) ($detail->religion ?? '');
        $this->localAddress      = (string) ($detail->local_address ?? '');
        $this->permanentAddress  = (string) ($detail->permanent_address ?? '');
        $this->selectedState     = $detail->state ?: null;
        $this->selectedCity      = $detail->city ?: null;
        $this->pincode           = (string) ($detail->pincode ?? '');
        $this->aadharNo          = (string) ($detail->aadhar_no ?? '');
        $this->transportationRequired = $detail->transportation_required ? '1' : '0';
        $this->selectedRoute     = $detail->transportations()->first()?->id;
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
        $this->deleteTargetId    = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    public function confirmDelete(): void
    {
        $detail = StudentDetail::find($this->deleteTargetId);

        if ($detail) {
            $user = User::find($detail->user_id);
            if ($user && $user->image) {
                $oldPath = parse_url($user->image, PHP_URL_PATH);
                Storage::disk('s3')->delete($oldPath);
            }
            $detail->delete();
            $user?->delete();
            $this->notification()->success('Student Deleted Successfully!');
            $this->loadStats();
            $this->resetPage();
        } else {
            $this->notification()->error('Student not found!');
        }

        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    // Keep old method as alias in case something still calls it
    public function doDeleteStudent($id = null): void
    {
        if ($id) $this->deleteTargetId = $id;
        $this->confirmDelete();
    }

    public function exportStudents(): StreamedResponse
    {
        $org      = Auth::user()->organization_id;
        $students = StudentDetail::with(['user', 'standard', 'section', 'organization', 'transportations'])
            ->whereHas('user', fn($q) => $q->where('organization_id', $org))
            ->orderBy('full_name')
            ->get();

        return response()->stream(function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Headers — mirror every field on the Add Student form, with
            // admission_no + roll_no front-and-centre and the organization name.
            fputcsv($handle, [
                'S.No',
                'Admission No',
                'Roll No',
                'Organization',
                'Full Name',
                'Email',
                'Mobile',
                'Gender',
                'Date of Birth',
                'Date of Admission',
                'Religion',
                'Aadhar No',
                'Father Name',
                'Mother Name',
                'Board (auto)',
                'Class',
                'Section',
                'Apaar ID',
                'Registration Number',
                'State',
                'City',
                'Pincode',
                'Local Address',
                'Permanent Address',
                'Transportation Required',
                'Transport Route',
                'Status',
            ]);

            foreach ($students as $index => $s) {
                $route = $s->transportations->first();
                fputcsv($handle, [
                    $index + 1,
                    $s->admission_no ?? '',
                    $s->roll_no ?? '',
                    $s->organization->name ?? '',
                    $s->full_name ?? '',
                    $s->user->email ?? '',
                    $s->phone ?? '',
                    ucfirst($s->gender ?? ''),
                    $s->dob?->format('d-m-Y') ?? '',
                    $s->date_of_admission?->format('d-m-Y') ?? '',
                    $s->religion ?? '',
                    $s->aadhar_no ?? '',
                    $s->father_name ?? '',
                    $s->mother_name ?? '',
                    $s->board ?? ($s->standard->board ?? ''),
                    $s->standard->name ?? '',
                    $s->section->name ?? '',
                    $s->appar_id ?? '',
                    $s->registration_number ?? '',
                    $s->state ?? '',
                    $s->city ?? '',
                    $s->pincode ?? '',
                    $s->local_address ?? '',
                    $s->permanent_address ?? '',
                    $s->transportation_required ? 'Yes' : 'No',
                    $route->route_name ?? '',
                    ($s->user->is_active ?? false) ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    /**
     * Admission number = YY + SCHOOL_CODE + lastDigit(class.code) + lastDigit(section.code) + 0000
     *   YY = last 2 digits of academic session (Apr→Mar). Before April it rolls back a year.
     *   SCHOOL_CODE = organization.school_code (e.g. "TDS")
     *   class.code / section.code: the model's "code" column. Missing codes fall back to a
     *     digit derived from the id so we never produce a malformed number.
     *   serial = 4 digits, per organization, starting at 0001.
     * Example: 26TDS010001
     */
    protected function generateAdmissionNumber(): string
    {
        $sessionYear = (int) (now()->month >= 4 ? now()->year : now()->subYear()->year);
        $yy          = substr((string) $sessionYear, -2);
        $schoolCode  = (string) (Auth::user()->organization->school_code ?? '');

        $classRow   = Standard::find((int) $this->studentsClass);
        $sectionRow = Section::find((int) $this->studentsSection);

        $classDigit   = $this->lastDigit($classRow?->code   ?? $classRow?->id);
        $sectionDigit = $this->lastDigit($sectionRow?->code ?? $sectionRow?->id);

        $prefix = $yy . $schoolCode . $classDigit . $sectionDigit;

        // Per-org serial: count rows whose admission_no starts with the same prefix.
        $last = StudentDetail::where('organization_id', Auth::user()->organization_id)
            ->where('admission_no', 'like', $prefix . '%')
            ->orderByDesc('admission_no')
            ->value('admission_no');

        $serial = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $serial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Roll number = 3-digit serial scoped to class + section. First student = "001".
     * Each class+section combination keeps its own sequence.
     */
    protected function generateRollNumber(): string
    {
        $last = StudentDetail::where('standard_id', (int) $this->studentsClass)
            ->where('section_id',  (int) $this->studentsSection)
            ->whereNotNull('roll_no')
            ->orderByRaw('CAST(roll_no AS UNSIGNED) DESC')
            ->value('roll_no');

        $serial = $last ? ((int) preg_replace('/\D/', '', $last)) + 1 : 1;

        return str_pad((string) $serial, 3, '0', STR_PAD_LEFT);
    }

    /** Pick the last numeric digit of a code-like value, fallback to "0". */
    protected function lastDigit($value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);
        if ($digits === '' || $digits === null) {
            return '0';
        }
        return substr($digits, -1);
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
            'selectedRoute',
            'apparId',
            'registrationNumber',
            'studentsActive',
        ]);
        $this->open     = false;
        $this->sections = [];
    }

    public function render()
    {
        $query = StudentDetail::with(['user', 'standard', 'section'])
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
            ));

        // Sorting — default is name A→Z. admission_no / roll_no use natural-ish
        // numeric ordering by casting to UNSIGNED so "9" sorts before "10".
        switch ($this->sortBy) {
            case 'admission_no':
                $query->orderByRaw('CAST(admission_no AS UNSIGNED) ASC')
                      ->orderBy('admission_no', 'asc');
                break;
            case 'roll_no':
                $query->orderByRaw('CAST(roll_no AS UNSIGNED) ASC')
                      ->orderBy('roll_no', 'asc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('full_name', 'asc');
                break;
        }

        $students = $query->paginate($this->perPage);

        return view('livewire.admin.student', compact('students'));
    }
}
