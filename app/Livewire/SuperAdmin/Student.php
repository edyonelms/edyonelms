<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Admin\SchoolInfo;
use App\Models\Organization;
use App\Models\Student\Section;
use App\Models\Student\Standard;
use App\Models\Student\StudentDetail;
use App\Models\User;
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

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $students = $this->baseQuery()->latest()->paginate($this->perPage);
        return view('livewire.super-admin.student', compact('students'));
    }
}
