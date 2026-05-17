<?php

namespace App\Livewire\Admin;

use App\Helpers\CityGetHelper;
use App\Models\Teacher\TeacherDetail;
use App\Models\Teacher\AssignTeacherStandard;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use App\Models\Admin\SchoolInfo;
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

class Teacher extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    // ─── Edit state ──────────────────────────────────────────────────────
    public $teacherData = [];

    // ─── Form Fields ─────────────────────────────────────────────────────
    public $dob              = '';
    public $teacherName      = '';
    public $teacherEmail     = '';
    public $teacherMobile    = '';
    public $teacherGender    = '';
    public $teacherActive    = 0;
    public $employeeId       = '';
    public $dateOfJoining    = '';
    public $qualification    = '';
    public $address          = '';
    public $pincode          = '';
    public $emergencyContact = '';

    // ─── Location ────────────────────────────────────────────────────────
    public $states        = [];
    public $cities        = [];
    public $selectedState = null;
    public $selectedCity  = null;

    // ─── Uploads ─────────────────────────────────────────────────────────
    public $teacherImage       = null;
    public $teacherDetailImage = null;
    public $teacherImageUrl    = null;

    // ─── Modal ───────────────────────────────────────────────────────────
    public $open          = false;
    public $openImage     = false;
    public $showViewModal = false;
    public $editId        = null;
    public $imagePath     = null;
    public $viewModalTitle = '';
    public $viewData       = [];

    // ─── Stats ───────────────────────────────────────────────────────────
    public $totalSchools     = 0;
    public $totalTeachers    = 0;
    public $activeTeachers   = 0;
    public $inactiveTeachers = 0;
    public $lastMonthJoining = 0;
    public $thisYearJoining  = 0;

    // ─── Search & Filters ────────────────────────────────────────────────
    public string $search        = '';
    public string $filterGender  = '';
    public string $filterStatus  = '';
    public string $filterClass   = '';
    public string $filterSection = '';
    public int    $perPage       = 25;

    // ─── Filter dependencies ─────────────────────────────────────────────
    public $standards      = [];
    public $filterSections = [];

    protected $queryString = [
        'search'        => ['except' => ''],
        'filterGender'  => ['except' => ''],
        'filterStatus'  => ['except' => ''],
        'filterClass'   => ['except' => ''],
        'filterSection' => ['except' => ''],
    ];

    protected $listeners = [
        'onViewTeacherAdmin',
        'onEditTeacher',
        'onDeleteTeacher',
        'onImageClick',
    ];

    public function mount(): void
    {
        $cityHelper       = new CityGetHelper();
        $this->states     = $cityHelper->getState();
        $this->standards  = Standard::where('organization_id', Auth::user()->organization_id)->get();
        $this->loadTeacherDashboardData();
    }

    public function loadTeacherDashboardData(): void
    {
        $org = Auth::user()->organization_id;

        $this->totalTeachers = TeacherDetail::where('organization_id', $org)->count();

        $this->lastMonthJoining = TeacherDetail::where('organization_id', $org)
            ->where('date_of_joining', '>=', now()->subMonth())->count();

        $academicYearStart = now()->month >= 3
            ? now()->startOfYear()->addMonths(2)->startOfMonth()
            : now()->subYear()->startOfYear()->addMonths(2)->startOfMonth();
        $this->thisYearJoining = TeacherDetail::where('organization_id', $org)
            ->where('date_of_joining', '>=', $academicYearStart)->count();

        $this->totalSchools = SchoolInfo::where('organization_id', $org)->count();

        $this->activeTeachers = User::where('organization_id', $org)
            ->where('role', 'teacher')->where('is_active', 1)->count();

        $this->inactiveTeachers = User::where('organization_id', $org)
            ->where('role', 'teacher')->where('is_active', 0)->count();
    }

    // ─── Filter resets ───────────────────────────────────────────────────
    public function updatedSearch(): void
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
    public function updatedFilterSection(): void
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
        $this->reset(['search', 'filterGender', 'filterStatus', 'filterClass', 'filterSection']);
        $this->filterSections = [];
        $this->resetPage();
    }

    // ─── Location ────────────────────────────────────────────────────────
    public function updatedSelectedState($value): void
    {
        $this->cities      = $value ? (new CityGetHelper())->cityGetByState($value) : [];
        $this->selectedCity = null;
    }

    // ─── Modal controls ──────────────────────────────────────────────────
    public function onAddTeacher(): void
    {
        $this->open = true;
    }

    public function closeModal(): void
    {
        $this->open = false;
        $this->resetForm();
        $this->dispatch('onUserAddUpdate');
    }

    public function closeViewModal(): void
    {
        $this->showViewModal  = false;
        $this->viewData       = [];
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

    // ─── Save ────────────────────────────────────────────────────────────
    public function onSave(): void
    {
        $rules = [
            'teacherName'      => 'required|string|max:255',
            'teacherEmail'     => 'required|email|max:50',
            'teacherMobile'    => 'required|string|digits:10',
            'dob'              => 'required|date|before:today',
            'teacherGender'    => 'required|string|in:male,female,other',
            'employeeId'       => 'required|string|max:50',
            'dateOfJoining'    => 'required|date|before_or_equal:today',
            'qualification'    => 'required|string|max:255',
            'address'          => 'required|string',
            'pincode'          => 'required|digits:6',
            'emergencyContact' => 'required|string|digits:10',
            'teacherImage'     => 'nullable|image|max:2048',
        ];

        // Unique email: exclude current user when editing, allow same email as student
        if ($this->editId) {
            $rules['teacherEmail'] .= '|unique:users,email,' . $this->editId . ',id,role,teacher';
        } else {
            $existingUser = User::where('email', $this->teacherEmail)->where('role', 'teacher')->first();
            if ($existingUser) {
                $this->addError('teacherEmail', 'A teacher with this email already exists.');
                return;
            }
        }

        $this->validate($rules);

        try {
            $isEdit = false;

            if ($this->editId) {
                $teacher = User::findOrFail($this->editId);
                $isEdit  = true;
            } else {
                $teacher = new User();
            }

            $teacher->name            = $this->teacherName;
            $teacher->email           = $this->teacherEmail;
            $teacher->mobile_number   = $this->teacherMobile;
            $teacher->dob             = $this->dob;
            $teacher->gender          = $this->teacherGender;
            $teacher->role            = 'teacher';
            $teacher->is_active       = $this->teacherActive ?? 0;
            $teacher->organization_id = Auth::user()->organization_id;

            if ($this->teacherImage) {
                if ($teacher->image) {
                    Storage::disk('s3')->delete(parse_url($teacher->image, PHP_URL_PATH));
                }
                $path = $this->teacherImage->store('teacher-images', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $teacher->image = Storage::disk('s3')->url($path);
            }

            if (!$isEdit) {
                $plainPassword = substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789@#$!'), 0, 10);
                $teacher->password = Hash::make($plainPassword);
            }

            $teacher->save();

            // Send password email only on creation
            if (!$isEdit) {
                try {
                    $teacherTemplateKey = config('services.zeptomail.teacher_password_template_key');
                    if ($teacherTemplateKey) {
                        $schoolName = Organization::find(Auth::user()->organization_id)?->name ?? 'School';
                        \App\Services\ZeptoMailService::sendTemplate(
                            $teacherTemplateKey,
                            $teacher->email,
                            $teacher->name,
                            [
                                'password'      => $plainPassword,
                                'email_address' => $teacher->email,
                                'school_name'   => $schoolName,
                                'username'      => $teacher->name,
                            ]
                        );
                    }
                } catch (\Throwable $e) {
                    logger()->error('Teacher password email failed: ' . $e->getMessage());
                }
            }

            TeacherDetail::updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'organization_id'   => Auth::user()->organization_id,
                    'employee_id'       => $this->employeeId,
                    'date_of_joining'   => $this->dateOfJoining,
                    'qualification'     => $this->qualification,
                    'phone'             => $this->teacherMobile,
                    'address'           => $this->address,
                    'city'              => $this->selectedCity,
                    'state'             => $this->selectedState,
                    'pincode'           => $this->pincode,
                    'emergency_contact' => $this->emergencyContact,
                ]
            );

            $this->notification()->success(
                $isEdit ? 'Teacher Updated Successfully!' : 'Teacher Created Successfully!'
            );

            $this->resetForm();
            $this->loadTeacherDashboardData();
            $this->resetPage();
            $this->dispatch('onTeacherAddUpdate');
        } catch (\Exception $e) {
            $this->notification()->error('Error Saving Teacher', $e->getMessage());
            logger()->error('Teacher save error: ' . $e->getMessage());
        }
    }

    // ─── View ────────────────────────────────────────────────────────────
    public function onViewTeacherAdmin($id): void
    {
        $detail = TeacherDetail::with('user')->find($id);

        if (!$detail || !$detail->user) {
            $this->notification()->error('Teacher not found!');
            return;
        }

        $assignments = AssignTeacherStandard::with(['standard', 'section'])
            ->where('teacher_detail_id', $detail->id)->get();

        $this->teacherImageUrl = $detail->user->image;
        $this->viewModalTitle  = 'Teacher Details';
        $this->viewData        = [
            'user'        => $detail->user,
            'detail'      => $detail,
            'assignments' => $assignments,
        ];
        $this->showViewModal = true;
    }

    // ─── Edit ────────────────────────────────────────────────────────────
    public function onEditTeacher($id): void
    {
        $detail = TeacherDetail::find($id);
        if (!$detail) {
            abort(404);
        }

        $user = User::find($detail->user_id);
        if (!$user) {
            abort(404);
        }

        $this->teacherData      = $user->toArray();
        $this->editId           = $user->id;
        $this->teacherName      = $user->name;
        $this->teacherEmail     = $user->email;
        $this->teacherMobile    = $user->mobile_number;
        $this->teacherActive    = $user->is_active;
        $this->dob              = $user->dob;
        $this->teacherGender    = $user->gender;
        $this->employeeId       = $detail->employee_id;
        $this->dateOfJoining    = $detail->date_of_joining;
        $this->qualification    = $detail->qualification;
        $this->address          = $detail->address;
        $this->selectedState    = $detail->state;
        $this->selectedCity     = $detail->city;
        $this->pincode          = $detail->pincode;
        $this->emergencyContact = $detail->emergency_contact;
        $this->teacherImageUrl  = $user->image;

        if ($this->selectedState) {
            $this->cities = (new CityGetHelper())->cityGetByState($this->selectedState);
        }

        $this->open = true;
        $this->dispatch('onUserAddUpdate');
    }

    // ─── Delete ──────────────────────────────────────────────────────────
    public function onDeleteTeacher($id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'icon'        => 'exclamation-circle',
            'iconColor'   => 'text-red-500',
            'description' => 'Are you sure you want to delete this teacher? This action cannot be undone.',
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'doDeleteTeacher',
                'params' => $id,
                'color'  => 'negative',
            ],
            'reject' => ['label' => 'No'],
        ]);
    }

    public function doDeleteTeacher($id): void
    {
        $detail = TeacherDetail::find($id);

        if ($detail) {
            AssignTeacherStandard::where('teacher_detail_id', $detail->id)->delete();
            $user = User::find($detail->user_id);
            $detail->delete();
            $user?->delete();
            $this->notification()->success('Teacher Deleted Successfully!');
            $this->loadTeacherDashboardData();
            $this->resetPage();
            $this->dispatch('onUserAddUpdate');
        } else {
            $this->notification()->error('Teacher not found!');
        }
    }

    // ─── Export ──────────────────────────────────────────────────────────
    public function exportTeachers(): StreamedResponse
    {
        $org      = Auth::user()->organization_id;
        $teachers = TeacherDetail::with('user')
            ->where('organization_id', $org)->get();

        return response()->stream(function () use ($teachers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'S.No',
                'Name',
                'Email',
                'Mobile',
                'Gender',
                'Date of Birth',
                'Employee ID',
                'Date of Joining',
                'Qualification',
                'Emergency Contact',
                'Address',
                'City',
                'State',
                'Pincode',
                'Status',
            ]);

            foreach ($teachers as $i => $t) {
                fputcsv($handle, [
                    $i + 1,
                    $t->user->name ?? '',
                    $t->user->email ?? '',
                    $t->user->mobile_number ?? '',
                    ucfirst($t->user->gender ?? ''),
                    $t->user->dob ?? '',
                    $t->employee_id ?? '',
                    $t->date_of_joining ?? '',
                    $t->qualification ?? '',
                    $t->emergency_contact ?? '',
                    $t->address ?? '',
                    $t->city ?? '',
                    $t->state ?? '',
                    $t->pincode ?? '',
                    ($t->user->is_active ?? false) ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="teachers_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ]);
    }

    // ─── Reset ───────────────────────────────────────────────────────────
    protected function resetForm(): void
    {
        $this->reset([
            'teacherData',
            'dob',
            'editId',
            'selectedState',
            'selectedCity',
            'teacherName',
            'teacherEmail',
            'teacherMobile',
            'teacherGender',
            'employeeId',
            'dateOfJoining',
            'qualification',
            'address',
            'pincode',
            'emergencyContact',
            'teacherActive',
            'teacherImage',
        ]);
        $this->open = false;
    }

    // ─── Render ──────────────────────────────────────────────────────────
    public function render()
    {
        $org = Auth::user()->organization_id;

        $teachers = TeacherDetail::with('user')
            ->where('organization_id', $org)
            ->when($this->search, fn($q) => $q->where(
                fn($q) => $q
                    ->where('employee_id', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'user',
                        fn($q) => $q
                            ->where('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%")
                    )
            ))
            ->when($this->filterGender, fn($q) => $q->whereHas(
                'user',
                fn($q) => $q->where('gender', $this->filterGender)
            ))
            ->when($this->filterStatus !== '', fn($q) => $q->whereHas(
                'user',
                fn($q) => $q->where('is_active', $this->filterStatus)
            ))
            ->when($this->filterClass, function ($q) {
                $teacherIds = AssignTeacherStandard::where('standard_id', $this->filterClass)
                    ->when($this->filterSection, fn($q) => $q->where('section_id', $this->filterSection))
                    ->pluck('teacher_detail_id');
                $q->whereIn('id', $teacherIds);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.teacher', compact('teachers'));
    }
}
