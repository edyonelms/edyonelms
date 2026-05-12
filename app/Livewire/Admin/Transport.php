<?php

namespace App\Livewire\Admin;

use App\Models\Admin\DriverDetail;
use App\Models\Admin\Transportation;
use App\Models\Organization;
use App\Models\Student\StudentDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Transport extends Component
{
    use WireUiActions, WithPagination;

    // ─── Tab ───────────────────────────────────────────────
    #[Url(keep: true)]
    public string $activeTab = 'transportation'; // transportation | drivers

    // ─── Modals ────────────────────────────────────────────
    public bool $driverModal      = false;
    public bool $transportModal   = false;
    public bool $assignStudentModal = false;

    // ─── Edit IDs ──────────────────────────────────────────
    public ?int $editDriverId      = null;
    public ?int $editTransportId   = null;
    public ?int $assignTransportId = null;

    // ─── Driver Form ───────────────────────────────────────
    public string $driver_name        = '';
    public string $driver_email       = '';
    public string $driver_phone       = '';
    public string $driver_password    = '';
    public string $license_no         = '';
    public string $driver_vehicle_no  = '';
    public string $driver_vehicle_type = '';
    public string $driver_address     = '';
    public int    $experience_years   = 0;
    public bool   $driver_is_active   = true;

    // ─── Transportation Form ───────────────────────────────
    public string  $route_name        = '';
    public ?int    $driver_detail_id  = null;
    public string  $pickup_location   = '';
    public string  $drop_location     = '';
    public string  $stops_input       = '';   // comma-separated
    public float   $monthly_fee       = 0;
    public int     $capacity          = 0;
    public bool    $transport_is_active = true;

    // ─── Assign Students ───────────────────────────────────
    public array  $selectedStudents   = [];
    public string $studentSearch      = '';

    // ─── Filters ───────────────────────────────────────────
    #[Url(keep: true)]
    public string $search = '';

    #[Url(keep: true)]
    public string $filterStatus = '';

    #[Url(keep: true)]
    public int $perPage = 10;

    // ─── Dropdown data (plain arrays — safe for Livewire serialization) ──
    public array $availableDrivers = [];
    public array $availableStudents = [];

    // Vehicle types
    public array $vehicleTypes = ['Bus', 'Mini Bus', 'Van', 'Auto', 'Car', 'Other'];

    protected $listeners = ['refresh-transport' => '$refresh'];

    public function mount(): void
    {
        $this->loadAvailableDrivers();
    }

    #[Computed(cache: true, key: 'transport-org-id', seconds: 3600)]
    public function organizationId(): ?int
    {
        return Auth::user()?->organization_id;
    }

    #[Computed]
    public function statistics(): array
    {
        if (!$this->organizationId) {
            return ['drivers' => 0, 'routes' => 0, 'students' => 0, 'monthly_revenue' => 0];
        }

        $orgId = $this->organizationId;

        return [
            'drivers'         => DriverDetail::where('organization_id', $orgId)->where('is_active', true)->count(),
            'routes'          => Transportation::where('organization_id', $orgId)->where('is_active', true)->count(),
            'students'        => DB::table('transportation_students')
                                    ->where('organization_id', $orgId)->count(),
            'monthly_revenue' => Transportation::where('organization_id', $orgId)
                                    ->where('is_active', true)
                                    ->withCount('students')
                                    ->get()
                                    ->sum(fn($t) => $t->monthly_fee * $t->students_count),
        ];
    }

    private function loadAvailableDrivers(): void
    {
        if (!$this->organizationId) {
            $this->availableDrivers = [];
            return;
        }

        $this->availableDrivers = DriverDetail::with('user:id,name')
            ->where('organization_id', $this->organizationId)
            ->where('is_active', true)
            ->get(['id', 'user_id', 'license_no', 'vehicle_no'])
            ->map(fn($d) => [
                'id'         => $d->id,
                'name'       => $d->user->name ?? 'Unknown',
                'license_no' => $d->license_no,
                'vehicle_no' => $d->vehicle_no,
            ])
            ->toArray();
    }

    private function loadAvailableStudents(): void
    {
        if (!$this->organizationId) {
            $this->availableStudents = [];
            return;
        }

        $query = StudentDetail::where('organization_id', $this->organizationId)
            ->where('transportation_required', true);

        if ($this->studentSearch) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->studentSearch . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->studentSearch . '%');
            });
        }

        $this->availableStudents = $query->orderBy('full_name')
            ->get(['id', 'full_name', 'admission_no', 'standard_id', 'section_id'])
            ->map(fn($s) => [
                'id'           => $s->id,
                'full_name'    => $s->full_name,
                'admission_no' => $s->admission_no,
            ])
            ->toArray();
    }

    // ─────────────────────────────────────────────────────────
    // Watchers
    // ─────────────────────────────────────────────────────────

    public function updatedSearch(): void          { $this->resetPage(); }
    public function updatedPerPage(): void         { $this->resetPage(); }
    public function updatedFilterStatus(): void    { $this->resetPage(); }
    public function updatedActiveTab(): void       { $this->resetPage(); $this->search = ''; }

    public function updatedStudentSearch(): void
    {
        $this->loadAvailableStudents();
    }

    public function createDriver(): void
    {
        $this->resetDriverForm();
        $this->editDriverId = null;
        $this->driverModal  = true;
    }

    public function editDriver(int $id): void
    {
        $driver = DriverDetail::with('user')->findOrFail($id);

        $this->editDriverId         = $driver->id;
        $this->driver_name          = $driver->user->name ?? '';
        $this->driver_email         = $driver->user->email ?? '';
        $this->driver_phone         = $driver->phone ?? '';
        $this->license_no           = $driver->license_no ?? '';
        $this->driver_vehicle_no    = $driver->vehicle_no ?? '';
        $this->driver_vehicle_type  = $driver->vehicle_type ?? '';
        $this->driver_address       = $driver->address ?? '';
        $this->experience_years     = $driver->experience_years ?? 0;
        $this->driver_is_active     = $driver->is_active;
        $this->driverModal          = true;
    }

    public function saveDriver(): void
    {
        $rules = [
            'driver_name'         => 'required|string|max:255',
            'driver_email'        => 'required|email|max:255',
            'driver_phone'        => 'nullable|string|max:20',
            'license_no'          => 'nullable|string|max:50',
            'driver_vehicle_no'   => 'nullable|string|max:30',
            'driver_vehicle_type' => 'nullable|string|max:50',
            'driver_address'      => 'nullable|string|max:500',
            'experience_years'    => 'nullable|integer|min:0|max:50',
        ];

        if (!$this->editDriverId) {
            $rules['driver_email']    = 'required|email|unique:users,email';
        }

        $this->validate($rules);

        DB::beginTransaction();
        try {
            if ($this->editDriverId) {
                $driver = DriverDetail::findOrFail($this->editDriverId);

                $driver->user->update([
                    'name'  => $this->driver_name,
                    'email' => $this->driver_email,
                ]);

                $driver->update([
                    'phone'            => $this->driver_phone,
                    'license_no'       => $this->license_no,
                    'vehicle_no'       => $this->driver_vehicle_no,
                    'vehicle_type'     => $this->driver_vehicle_type,
                    'address'          => $this->driver_address,
                    'experience_years' => $this->experience_years,
                    'is_active'        => $this->driver_is_active,
                ]);
            } else {
                $user = User::create([
                    'name'            => $this->driver_name,
                    'email'           => $this->driver_email,
                    'mobile_number'   => $this->driver_phone,
                    'password'        => Hash::make("123456"), // Default password, should be changed by driver
                    'role'            => 'driver',
                    'organization_id' => $this->organizationId,
                    'is_active'       => true,
                ]);

                DriverDetail::create([
                    'user_id'          => $user->id,
                    'organization_id'  => $this->organizationId,
                    'phone'            => $this->driver_phone,
                    'license_no'       => $this->license_no,
                    'vehicle_no'       => $this->driver_vehicle_no,
                    'vehicle_type'     => $this->driver_vehicle_type,
                    'address'          => $this->driver_address,
                    'experience_years' => $this->experience_years,
                    'is_active'        => true,
                ]);
            }

            DB::commit();

            $this->loadAvailableDrivers();
            unset($this->statistics);

            $this->notification()->success(
                title: 'Success!',
                description: $this->editDriverId ? 'Driver updated successfully' : 'Driver added successfully'
            );

            $this->closeDriverModal();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(title: 'Error!', description: 'Failed to save driver: ' . $e->getMessage());
            logger()->error('Save driver error: ' . $e->getMessage());
        }
    }

    public function deleteDriver(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Driver?',
            'description' => 'This will remove the driver and their user account. Assigned routes will have no driver.',
            'icon'        => 'error',
            'accept'      => ['label' => 'Yes, Delete', 'method' => 'confirmDeleteDriver', 'params' => $id],
            'reject'      => ['label' => 'Cancel'],
        ]);
    }

    public function confirmDeleteDriver(int $id): void
    {
        DB::beginTransaction();
        try {
            $driver = DriverDetail::with('user')->findOrFail($id);
            $driver->user?->delete();
            $driver->delete();

            DB::commit();

            $this->loadAvailableDrivers();
            unset($this->statistics);

            $this->notification()->success(title: 'Deleted!', description: 'Driver removed successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->notification()->error(title: 'Error!', description: 'Failed to delete driver');
            logger()->error('Delete driver error: ' . $e->getMessage());
        }
    }

    public function toggleDriverStatus(int $id): void
    {
        $driver = DriverDetail::findOrFail($id);
        $driver->update(['is_active' => !$driver->is_active]);
        $driver->user?->update(['is_active' => !$driver->user->is_active]);
        $this->loadAvailableDrivers();
        unset($this->statistics);
        $this->notification()->success(title: 'Updated!', description: 'Driver status changed');
    }

    public function closeDriverModal(): void
    {
        $this->driverModal = false;
        $this->editDriverId = null;
        $this->resetDriverForm();
        $this->resetValidation();
    }

    private function resetDriverForm(): void
    {
        $this->reset([
            'driver_name', 'driver_email', 'driver_phone', 'driver_password',
            'license_no', 'driver_vehicle_no', 'driver_vehicle_type',
            'driver_address', 'experience_years', 'driver_is_active',
        ]);
        $this->driver_is_active = true;
    }

    public function createTransport(): void
    {
        $this->resetTransportForm();
        $this->editTransportId = null;
        $this->transportModal  = true;
    }

    public function editTransport(int $id): void
    {
        $transport = Transportation::findOrFail($id);

        $this->editTransportId      = $transport->id;
        $this->route_name           = $transport->route_name;
        $this->driver_detail_id     = $transport->driver_detail_id;
        $this->pickup_location      = $transport->pickup_location ?? '';
        $this->drop_location        = $transport->drop_location ?? '';
        $this->stops_input          = is_array($transport->stops) ? implode(', ', $transport->stops) : '';
        $this->monthly_fee          = (float) $transport->monthly_fee;
        $this->capacity             = (int) $transport->capacity;
        $this->transport_is_active  = $transport->is_active;
        $this->transportModal       = true;
    }

    public function saveTransport(): void
    {
        $this->validate([
            'route_name'       => 'required|string|max:255',
            'driver_detail_id' => 'required|exists:driver_details,id',
            'pickup_location'  => 'nullable|string|max:255',
            'drop_location'    => 'nullable|string|max:255',
            'monthly_fee'      => 'nullable|numeric|min:0',
            'capacity'         => 'nullable|integer|min:0',
        ]);

        $stops  = array_filter(array_map('trim', explode(',', $this->stops_input)));

        // Pull vehicle info from the assigned driver
        $driver = DriverDetail::find($this->driver_detail_id);

        $data = [
            'organization_id'  => $this->organizationId,
            'route_name'       => $this->route_name,
            'vehicle_no'       => $driver?->vehicle_no,
            'vehicle_type'     => $driver?->vehicle_type,
            'driver_detail_id' => $this->driver_detail_id,
            'pickup_location'  => $this->pickup_location ?: null,
            'drop_location'    => $this->drop_location ?: null,
            'stops'            => !empty($stops) ? array_values($stops) : null,
            'monthly_fee'      => $this->monthly_fee,
            'capacity'         => $this->capacity,
            'is_active'        => $this->transport_is_active,
        ];

        try {
            if ($this->editTransportId) {
                Transportation::findOrFail($this->editTransportId)->update($data);
            } else {
                Transportation::create($data);
            }

            unset($this->statistics);

            $this->notification()->success(
                title: 'Success!',
                description: $this->editTransportId ? 'Route updated successfully' : 'Route created successfully'
            );

            $this->closeTransportModal();
        } catch (\Exception $e) {
            $this->notification()->error(title: 'Error!', description: 'Failed to save route: ' . $e->getMessage());
            logger()->error('Save transport error: ' . $e->getMessage());
        }
    }

    public function deleteTransport(int $id): void
    {
        $this->dialog()->confirm([
            'title'       => 'Delete Route?',
            'description' => 'This will remove the route and unassign all students.',
            'icon'        => 'error',
            'accept'      => ['label' => 'Yes, Delete', 'method' => 'confirmDeleteTransport', 'params' => $id],
            'reject'      => ['label' => 'Cancel'],
        ]);
    }

    public function confirmDeleteTransport(int $id): void
    {
        try {
            Transportation::findOrFail($id)->delete();
            unset($this->statistics);
            $this->notification()->success(title: 'Deleted!', description: 'Route deleted successfully');
        } catch (\Exception $e) {
            $this->notification()->error(title: 'Error!', description: 'Failed to delete route');
        }
    }

    public function toggleTransportStatus(int $id): void
    {
        $t = Transportation::findOrFail($id);
        $t->update(['is_active' => !$t->is_active]);
        unset($this->statistics);
        $this->notification()->success(title: 'Updated!', description: 'Route status changed');
    }

    public function closeTransportModal(): void
    {
        $this->transportModal  = false;
        $this->editTransportId = null;
        $this->resetTransportForm();
        $this->resetValidation();
    }

    private function resetTransportForm(): void
    {
        $this->reset([
            'route_name', 'driver_detail_id',
            'pickup_location', 'drop_location', 'stops_input',
            'monthly_fee', 'capacity', 'transport_is_active',
        ]);
        $this->transport_is_active = true;
        $this->monthly_fee         = 0;
        $this->capacity            = 0;
    }

    public function openAssignStudents(int $transportId): void
    {
        $this->assignTransportId = $transportId;
        $this->studentSearch     = '';

        // Pre-select already assigned students
        $this->selectedStudents = Transportation::findOrFail($transportId)
            ->students()
            ->pluck('student_details.id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->loadAvailableStudents();
        $this->assignStudentModal = true;
    }

    public function saveAssignedStudents(): void
    {
        if (!$this->assignTransportId) return;

        try {
            $transport = Transportation::findOrFail($this->assignTransportId);

            // Sync selected students with org_id in pivot
            $syncData = collect($this->selectedStudents)
                ->mapWithKeys(fn($id) => [(int)$id => ['organization_id' => $this->organizationId]])
                ->toArray();

            $transport->students()->sync($syncData);

            unset($this->statistics);

            $this->notification()->success(
                title: 'Success!',
                description: count($this->selectedStudents) . ' student(s) assigned'
            );

            $this->closeAssignStudentModal();
        } catch (\Exception $e) {
            $this->notification()->error(title: 'Error!', description: 'Failed to assign students');
            logger()->error('Assign students error: ' . $e->getMessage());
        }
    }

    public function removeStudent(int $transportId, int $studentDetailId): void
    {
        Transportation::findOrFail($transportId)->students()->detach($studentDetailId);
        unset($this->statistics);
        $this->notification()->success(title: 'Removed!', description: 'Student unassigned');
    }

    public function closeAssignStudentModal(): void
    {
        $this->assignStudentModal = false;
        $this->assignTransportId  = null;
        $this->selectedStudents   = [];
        $this->studentSearch      = '';
        $this->availableStudents  = [];
    }

    public function render()
    {
        return view('livewire.admin.transport', [
            'transportations' => $this->getTransportations(),
            'drivers'         => $this->getDrivers(),
        ]);
    }

    private function getTransportations()
    {
        if (!$this->organizationId) return collect()->paginate($this->perPage);

        $query = Transportation::with(['driver.user', 'students'])
            ->where('organization_id', $this->organizationId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('route_name', 'like', '%' . $this->search . '%')
                  ->orWhere('vehicle_no', 'like', '%' . $this->search . '%')
                  ->orWhere('pickup_location', 'like', '%' . $this->search . '%')
                  ->orWhere('drop_location', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', (bool) $this->filterStatus);
        }

        return $query->orderBy('route_name')->paginate($this->perPage);
    }

    private function getDrivers()
    {
        if (!$this->organizationId) return collect()->paginate($this->perPage);

        $query = DriverDetail::with(['user', 'transportations'])
            ->where('organization_id', $this->organizationId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('license_no', 'like', '%' . $this->search . '%')
                  ->orWhere('vehicle_no', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', (bool) $this->filterStatus);
        }

        return $query->orderByDesc('created_at')->paginate($this->perPage);
    }
}