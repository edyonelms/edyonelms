<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Organization;
use App\Models\Student\StudentDetail;
use App\Models\Teacher\TeacherDetail;
use App\Models\User;
use App\Services\ZeptoMailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Schools extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    public int $totalSchools   = 0;
    public int $activeSchools  = 0;
    public int $pendingSchools = 0;
    public int $totalStudents  = 0;
    public int $totalTeachers  = 0;
    public     $avgStudents    = 0;

    public string $search = '';

    public string $activeView   = 'list';
    public        $detailSchool = null;
    public string $detailTab    = 'overview';

    // ── Selected rows for analytics ───────────────────────────────────────────
    public $selectedStudentId = null;
    public $selectedTeacherId = null;

    public bool $showModal   = false;
    public      $editId      = null;
    public      $adminUserId = null;

    public $schoolName     = '';
    public $email          = '';
    public $mobileNumber   = '';
    public $state          = '';
    public $educationBoard = '';
    public $schoolCode     = '';
    public $affiliationNo  = '';
    public $udiseNumber    = '';
    public $serialNumber   = '';
    public $address        = '';
    public $logo;
    public $existingLogo   = null;

    public bool   $showBankModal   = false;
    public bool   $editBankMode    = false;

    public bool   $showDeleteConfirm = false;
    public        $deleteTargetId    = null;
    public string $bankName       = '';
    public string $bankAccountNo  = '';
    public string $bankIfsc       = '';
    public string $bankBranch     = '';
    public string $bankHolderName = '';

    protected $listeners = [
        'editSchool'   => 'onEdit',
        'deleteSchool' => 'onDelete',
    ];

    public function mount(): void
    {
        $this->loadStats();
    }

    private function loadStats(): void
    {
        $this->totalSchools   = Organization::count();
        $this->activeSchools  = Organization::where('status', true)->count();
        $this->pendingSchools = Organization::where('status', false)->count();
        $this->totalStudents  = StudentDetail::count();
        $this->totalTeachers  = TeacherDetail::count();
        $this->avgStudents    = $this->totalSchools > 0
            ? round($this->totalStudents / $this->totalSchools) : 0;
    }

    // ─── Search ───────────────────────────────────────────────────────────────

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // ─── Detail View ──────────────────────────────────────────────────────────

    public function viewSchoolDetail($id): void
    {
        $this->detailSchool = Organization::withCount([
            'students as total_students',
            'teachers as total_teachers',
        ])->find($id);

        if (!$this->detailSchool) {
            $this->notification()->error('School not found!');
            return;
        }
        $this->detailTab          = 'overview';
        $this->activeView         = 'detail';
        $this->selectedStudentId  = null;
        $this->selectedTeacherId  = null;
    }

    public function backToList(): void
    {
        $this->activeView         = 'list';
        $this->detailSchool       = null;
        $this->detailTab          = 'overview';
        $this->selectedStudentId  = null;
        $this->selectedTeacherId  = null;
    }

    public function setDetailTab(string $tab): void
    {
        $this->detailTab         = $tab;
        $this->selectedStudentId = null;
        $this->selectedTeacherId = null;
    }

    // ─── Student / Teacher selection ──────────────────────────────────────────

    public function selectStudent($id): void
    {
        $this->selectedStudentId = ($this->selectedStudentId == $id) ? null : $id;
    }

    public function selectTeacher($id): void
    {
        $this->selectedTeacherId = ($this->selectedTeacherId == $id) ? null : $id;
    }

    // ─── Bank Details ─────────────────────────────────────────────────────────

    public function openBankModal(): void
    {
        if ($this->detailSchool) {
            $this->bankName       = $this->detailSchool->bank_name ?? '';
            $this->bankAccountNo  = $this->detailSchool->bank_account_no ?? '';
            $this->bankIfsc       = $this->detailSchool->bank_ifsc ?? '';
            $this->bankBranch     = $this->detailSchool->bank_branch ?? '';
            $this->bankHolderName = $this->detailSchool->bank_holder_name ?? '';
            $this->editBankMode   = !empty($this->detailSchool->bank_name);
        }
        $this->showBankModal = true;
    }

    public function closeBankModal(): void
    {
        $this->showBankModal = false;
        $this->reset(['bankName', 'bankAccountNo', 'bankIfsc', 'bankBranch', 'bankHolderName']);
    }

    public function saveBankDetails(): void
    {
        $this->validate([
            'bankName'       => 'required|string|max:255',
            'bankAccountNo'  => 'required|string|max:50',
            'bankIfsc'       => 'required|string|max:20',
            'bankBranch'     => 'required|string|max:255',
            'bankHolderName' => 'required|string|max:255',
        ]);

        Organization::where('id', $this->detailSchool->id)->update([
            'bank_name'        => $this->bankName,
            'bank_account_no'  => $this->bankAccountNo,
            'bank_ifsc'        => strtoupper($this->bankIfsc),
            'bank_branch'      => $this->bankBranch,
            'bank_holder_name' => $this->bankHolderName,
        ]);

        $this->detailSchool = Organization::withCount([
            'students as total_students',
            'teachers as total_teachers',
        ])->find($this->detailSchool->id);

        $this->closeBankModal();
        $this->notification()->success('Bank details saved successfully!');
    }

    // ─── School CRUD ──────────────────────────────────────────────────────────

    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function onEdit($id): void
    {
        $school = Organization::find($id);
        if (!$school) return;

        $this->editId         = $id;
        $this->schoolName     = $school->name;
        $this->email          = $school->email;
        $this->mobileNumber   = $school->mobile_number;
        $this->state          = $school->state;
        $this->educationBoard = $school->education_board;
        $this->schoolCode     = $school->school_code;
        $this->affiliationNo  = $school->affiliation_no;
        $this->udiseNumber    = $school->udise_number;
        $this->serialNumber   = $school->serial_number;
        $this->address        = $school->address;
        $this->existingLogo   = $school->logo;

        $this->adminUserId = User::where('organization_id', $id)
            ->where('role', 'admin')
            ->value('id');

        $this->showModal = true;
    }

    public function saveSchool(): void
    {
        $this->validate([
            'schoolName'     => 'required|string|max:255',
            'email'          => [
                'required',
                'email',
                Rule::unique('organizations', 'email')->ignore($this->editId),
                Rule::unique('users', 'email')->ignore($this->adminUserId),
            ],
            'mobileNumber'   => 'required|string|max:15',
            'state'          => 'required|string',
            'educationBoard' => 'required|string',
            'schoolCode'     => [
                'required',
                'string',
                Rule::unique('organizations', 'school_code')->ignore($this->editId),
            ],
            'serialNumber'   => [
                'required',
                'string',
                Rule::unique('organizations', 'serial_number')->ignore($this->editId),
            ],
            'affiliationNo'  => 'nullable|string|max:100',
            'udiseNumber'    => 'nullable|string|max:100',
            'logo'           => 'nullable|image|max:2048',
        ]);

        $plainPassword = null;

        DB::transaction(function () use (&$plainPassword) {
            $logoUrl = $this->existingLogo;

            if ($this->logo) {
                $path    = $this->logo->store('superadmin/schools/logos', 's3');
                Storage::disk('s3')->setVisibility($path, 'public');
                $logoUrl = Storage::disk('s3')->url($path);
            }

            $orgData = [
                'name'            => $this->schoolName,
                'email'           => $this->email,
                'mobile_number'   => $this->mobileNumber,
                'state'           => $this->state,
                'education_board' => $this->educationBoard,
                'school_code'     => $this->schoolCode,
                'affiliation_no'  => $this->affiliationNo,
                'udise_number'    => $this->udiseNumber,
                'serial_number'   => $this->serialNumber,
                'address'         => $this->address,
                'logo'            => $logoUrl,
                'status'          => true,
            ];

            if ($this->editId) {
                Organization::findOrFail($this->editId)->update($orgData);
                if ($this->adminUserId) {
                    User::where('id', $this->adminUserId)->update([
                        'email'         => $this->email,
                        'mobile_number' => $this->mobileNumber,
                    ]);
                }
            } else {
                $org = Organization::create($orgData);

                $plainPassword = Str::upper(Str::random(4)) . rand(100, 999) . Str::random(3);

                User::create([
                    'name'            => $this->schoolName . ' Admin',
                    'email'           => $this->email,
                    'mobile_number'   => $this->mobileNumber,
                    'organization_id' => $org->id,
                    'role'            => 'admin',
                    'password'        => Hash::make($plainPassword),
                ]);
            }
        });

        // Send welcome email after transaction so a mail failure never rolls back the school record
        if ($plainPassword !== null) {
            $templateKey = config('services.zeptomail.school_creation_template_key');
            if ($templateKey) {
                try {
                    ZeptoMailService::sendTemplate(
                        $templateKey,
                        $this->email,
                        $this->schoolName . ' Admin',
                        [
                            'school_name' => $this->schoolName,
                            'email'       => $this->email,
                            'password'    => $plainPassword,
                        ]
                    );
                } catch (\Throwable $e) {
                    Log::error('School creation welcome email failed', [
                        'email' => $this->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->closeModal();
        $this->loadStats();
        $this->resetPage();
        $this->notification()->success('School saved successfully!');
    }

    public function onDelete($id): void
    {
        $this->deleteTargetId    = $id;
        $this->showDeleteConfirm = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;
    }

    // ─── Login as School Admin ────────────────────────────────────────────────

    public function loginAsSchool($orgId): mixed
    {
        $admin = User::where('organization_id', $orgId)
            ->where('role', 'admin')
            ->first();

        if (!$admin) {
            $this->notification()->error('No admin account found for this school.');
            return null;
        }

        Auth::login($admin);
        return redirect()->route('admin.home', ['organization' => $admin->organization_id]);
    }

    public function doDelete($id): void
    {
        Organization::find($id)?->delete();
        User::where('organization_id', $id)->where('role', 'admin')->delete();
        \App\Models\Admin\RateLms::where('organization_id', $id)->delete();

        $this->showDeleteConfirm = false;
        $this->deleteTargetId    = null;

        if ($this->activeView === 'detail' && $this->detailSchool?->id == $id) {
            $this->backToList();
        }

        $this->loadStats();
        $this->resetPage();
        $this->notification()->success('School deleted successfully!');
    }

    private function resetForm(): void
    {
        $this->reset([
            'schoolName',
            'email',
            'mobileNumber',
            'state',
            'educationBoard',
            'schoolCode',
            'affiliationNo',
            'udiseNumber',
            'serialNumber',
            'address',
            'logo',
            'existingLogo',
            'editId',
            'adminUserId',
        ]);
    }

    public function render()
    {
        $schools = Organization::withCount([
            'students as total_students',
            'teachers as total_teachers',
        ])
            ->when($this->search, fn($q) => $q->where(
                fn($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('school_code', 'like', "%{$this->search}%")
                    ->orWhere('affiliation_no', 'like', "%{$this->search}%")
            ))
            ->latest()
            ->paginate(12);

        // ── Fee Stats ─────────────────────────────────────────────────────────
        $feeStats = [];
        if ($this->activeView === 'detail' && $this->detailSchool && $this->detailTab === 'fees') {
            $orgId = $this->detailSchool->id;

            // Financial year: April 2026 → March 2027
            $fyMonthlyChart = FeePayment::forOrg($orgId)
                ->where(function ($q) {
                    $q->where(function ($q) {
                        $q->whereYear('payment_date', 2026)
                            ->whereMonth('payment_date', '>=', 4);
                    })->orWhere(function ($q) {
                        $q->whereYear('payment_date', 2027)
                            ->whereMonth('payment_date', '<=', 3);
                    });
                })
                ->selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(amount) as total')
                ->groupBy('month', 'year')
                ->get()
                ->mapWithKeys(fn($row) => ["{$row->year}-{$row->month}" => (float) $row->total])
                ->toArray();

            $feeStats = [
                'total_collected'    => FeePayment::forOrg($orgId)->sum('amount'),
                'this_month'         => FeePayment::forOrg($orgId)
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
                'last_month'         => FeePayment::forOrg($orgId)
                    ->whereMonth('payment_date', now()->subMonth()->month)
                    ->whereYear('payment_date', now()->subMonth()->year)
                    ->sum('amount'),
                'this_year'          => FeePayment::forOrg($orgId)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
                'academic_total'     => FeePayment::forOrg($orgId)->academic()->sum('amount'),
                'transport_total'    => FeePayment::forOrg($orgId)->transport()->sum('amount'),
                'total_transactions' => FeePayment::forOrg($orgId)->count(),
                'this_month_count'   => FeePayment::forOrg($orgId)
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->count(),
                'recent_payments'    => FeePayment::forOrg($orgId)
                    ->with(['studentDetail.user', 'standard', 'section'])
                    ->latest('payment_date')
                    ->take(10)
                    ->get(),
                'fy_monthly_chart'   => $fyMonthlyChart,
                // Adjust this query to match your FeeStructure model
                'total_to_collect'   => 0,
            ];
        }

        // ── Student Stats ─────────────────────────────────────────────────────
        $studentStats = [];
        if ($this->activeView === 'detail' && $this->detailSchool && $this->detailTab === 'students') {
            $orgId = $this->detailSchool->id;
            $studentStats = [
                'total'    => StudentDetail::where('organization_id', $orgId)->count(),
                'active'   => StudentDetail::where('organization_id', $orgId)
                    ->whereHas('user', fn($q) => $q->where('is_active', true))->count(),
                'inactive' => StudentDetail::where('organization_id', $orgId)
                    ->whereHas('user', fn($q) => $q->where('is_active', false))->count(),
                'male'     => StudentDetail::where('organization_id', $orgId)
                    ->whereRaw('LOWER(gender) = ?', ['male'])->count(),
                'female'   => StudentDetail::where('organization_id', $orgId)
                    ->whereRaw('LOWER(gender) = ?', ['female'])->count(),
                'selected' => $this->selectedStudentId
                    ? StudentDetail::with(['user', 'standard', 'section'])
                    ->find($this->selectedStudentId)
                    : null,
            ];
        }

        // ── Teacher Stats ─────────────────────────────────────────────────────
        $teacherStats = [];
        if ($this->activeView === 'detail' && $this->detailSchool && $this->detailTab === 'teachers') {
            $orgId = $this->detailSchool->id;
            $teacherStats = [
                'total'    => TeacherDetail::where('organization_id', $orgId)->count(),
                'active'   => TeacherDetail::where('organization_id', $orgId)
                    ->whereHas('user', fn($q) => $q->where('is_active', true))->count(),
                'inactive' => TeacherDetail::where('organization_id', $orgId)
                    ->whereHas('user', fn($q) => $q->where('is_active', false))->count(),
                'selected' => $this->selectedTeacherId
                    ? TeacherDetail::with(['user', 'assignedSubjects.subject'])
                    ->find($this->selectedTeacherId)
                    : null,
            ];
        }

        return view('livewire.super-admin.schools', compact(
            'schools',
            'feeStats',
            'studentStats',
            'teacherStats'
        ));
    }
}
