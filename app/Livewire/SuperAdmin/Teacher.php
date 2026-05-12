<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Organization;
use App\Models\Teacher\TeacherDetail;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Teacher extends Component
{
    use WireUiActions, WithPagination;

    public int $totalSchools      = 0;
    public int $totalTeachers     = 0;
    public int $activeTeachers    = 0;
    public int $inactiveTeachers  = 0;

    public bool   $showViewModal  = false;
    public string $viewModalTitle = '';
    public array  $viewData       = [];
    public        $teacherImageUrl = null;

    public string $search             = '';
    public string $filterOrganization = '';
    public string $filterGender       = '';
    public string $filterStatus       = '';
    public int    $perPage            = 50;

    public $organizations = [];

    protected $queryString = [
        'search'             => ['except' => ''],
        'filterOrganization' => ['except' => ''],
        'filterGender'       => ['except' => ''],
        'filterStatus'       => ['except' => ''],
    ];

    protected $listeners = ['onViewTeacherSuperAdmin' => 'viewTeacher', 'onDeleteTeacherSuperAdmin'];


    public function mount(): void
    {
        $this->organizations = Organization::orderBy('name')->get();
        $this->loadStats();
    }


    private function baseQuery()
    {
        return TeacherDetail::with(['user', 'user.organization', 'assignedSubjects.subject'])
            ->when($this->filterOrganization, fn($q) => $q->whereHas('user', fn($q) => $q->where('organization_id', $this->filterOrganization)))
            ->when($this->search, fn($q) => $q->where(fn($q) => $q
                ->where('employee_id', 'like', "%{$this->search}%")
                ->orWhere('qualification', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('mobile_number', 'like', "%{$this->search}%")
                )
            ))
            ->when($this->filterGender, fn($q) => $q->whereHas('user', fn($q) => $q->where('gender', $this->filterGender)))
            ->when($this->filterStatus !== '', fn($q) => $q->whereHas('user', fn($q) => $q->where('is_active', $this->filterStatus)));
    }

    private function loadStats(): void
    {
        $this->totalSchools     = Organization::count();
        $this->totalTeachers    = $this->baseQuery()->count();
        $this->activeTeachers   = $this->baseQuery()->whereHas('user', fn($q) => $q->where('is_active', 1))->count();
        $this->inactiveTeachers = $this->baseQuery()->whereHas('user', fn($q) => $q->where('is_active', 0))->count();
    }


    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->loadStats();
    }

    public function updatedFilterOrganization(): void
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
        $this->reset(['search', 'filterOrganization', 'filterGender', 'filterStatus']);
        $this->resetPage();
        $this->loadStats();
    }

    // ─── View ─────────────────────────────────────────────────────────────────

    public function viewTeacher($id): void
    {
        $user = User::with([
            'teacherDetail',
            'teacherDetail.assignedSubjects.subject',
            'organization',
        ])->find($id);

        if (!$user) {
            $this->notification()->error('Teacher not found!');
            return;
        }

        $detail   = $user->teacherDetail ?? null;
        $subjects = $detail?->assignedSubjects?->count()
            ? $detail->assignedSubjects->pluck('subject.name')->filter()->implode(', ')
            : '—';

        $this->teacherImageUrl = $user->image ?? null;
        $this->viewModalTitle  = 'Teacher Details';
        $this->viewData        = [
            'user'        => $user,
            'detail'      => $detail,
            'subjects'    => $subjects,
            'school_name' => $user->organization?->name ?? '—',
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

    public function onDeleteTeacherSuperAdmin($id): void
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
            $user = User::find($detail->user_id);
            $detail->delete();
            $user?->delete();
            $this->loadStats();
            $this->resetPage();
            $this->notification()->success('Teacher deleted successfully!');
        } else {
            $this->notification()->error('Teacher not found!');
        }
    }

    // ─── Export ───────────────────────────────────────────────────────────────

    public function exportTeachers()
    {
        if (!$this->filterOrganization) return;

        $teachers = $this->baseQuery()->get();

        return response()->streamDownload(function () use ($teachers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'S.No', 'Name', 'Email', 'Mobile', 'Gender',
                'Employee ID', 'Date of Birth', 'Date of Joining',
                'Qualification', 'Emergency Contact',
                'Address', 'City', 'State', 'Pincode',
                'Subjects', 'School', 'Status',
            ]);

            foreach ($teachers as $i => $t) {
                $subjects = $t->assignedSubjects?->count()
                    ? $t->assignedSubjects->pluck('subject.name')->filter()->implode(', ')
                    : '';

                fputcsv($handle, [
                    $i + 1,
                    $t->user?->name ?? '',
                    $t->user?->email ?? '',
                    $t->user?->mobile_number ?? '',
                    ucfirst($t->user?->gender ?? ''),
                    $t->employee_id ?? '',
                    $t->user?->dob ?? '',
                    $t->date_of_joining ?? '',
                    $t->qualification ?? '',
                    $t->emergency_contact ?? '',
                    $t->address ?? '',
                    $t->city ?? '',
                    $t->state ?? '',
                    $t->pincode ?? '',
                    $subjects,
                    $t->user?->organization?->name ?? '',
                    ($t->user?->is_active) ? 'Active' : 'Inactive',
                ]);
            }
            fclose($handle);
        }, 'teachers_' . now()->format('Y-m-d_H-i-s') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $teachers = $this->baseQuery()->latest()->paginate($this->perPage);
        return view('livewire.super-admin.teacher', compact('teachers'));
    }
}