<?php

namespace App\Livewire\Accounts;

use App\Models\Admin\DriverDetail;
use App\Models\Admin\Transportation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Transport extends Component
{
    use WithPagination;

    // ─── Filters ───────────────────────────────────────────────
    public string $search       = '';
    public string $filterRoute  = '';
    public string $filterStatus = '';
    public int    $perPage      = 15;

    // ─── View Modal ────────────────────────────────────────────
    public bool  $viewModal    = false;
    public ?int  $viewDriverId = null;

    protected $listeners = ['refresh' => '$refresh'];

    public function mount(): void {}

    #[Computed(cache: true, key: 'acc-transport-org', seconds: 3600)]
    public function organizationId(): ?int
    {
        return Auth::user()?->organization_id;
    }

    #[Computed]
    public function statistics(): array
    {
        if (!$this->organizationId) {
            return ['drivers' => 0, 'routes' => 0, 'active_routes' => 0];
        }

        $orgId = $this->organizationId;

        return [
            'drivers'       => DriverDetail::where('organization_id', $orgId)->where('is_active', true)->count(),
            'routes'        => Transportation::where('organization_id', $orgId)->count(),
            'active_routes' => Transportation::where('organization_id', $orgId)->where('is_active', true)->count(),
        ];
    }

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterRoute(): void  { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedPerPage(): void  { $this->resetPage(); }

    public function openViewModal(int $driverId): void
    {
        $this->viewDriverId = $driverId;
        $this->viewModal    = true;
    }

    public function closeViewModal(): void
    {
        $this->viewModal    = false;
        $this->viewDriverId = null;
    }

    #[Computed]
    public function viewDriver()
    {
        if (!$this->viewDriverId) return null;
        return DriverDetail::with(['user', 'transportations.students'])
            ->find($this->viewDriverId);
    }

    #[Computed]
    public function routeOptions(): array
    {
        if (!$this->organizationId) return [];
        return Transportation::where('organization_id', $this->organizationId)
            ->orderBy('route_name')
            ->pluck('route_name', 'route_name')
            ->toArray();
    }

    public function render()
    {
        $drivers = $this->getDrivers();

        return view('livewire.accounts.transport', compact('drivers'));
    }

    private function getDrivers()
    {
        if (!$this->organizationId) {
            return collect()->paginate($this->perPage);
        }

        $query = DriverDetail::with(['user', 'transportations'])
            ->where('organization_id', $this->organizationId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('license_no', 'like', '%' . $this->search . '%')
                  ->orWhere('vehicle_no', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', fn($uq) =>
                      $uq->where('name', 'like', '%' . $this->search . '%')
                  );
            });
        }

        if ($this->filterRoute) {
            $query->whereHas('transportations', fn($q) =>
                $q->where('route_name', $this->filterRoute)
            );
        }

        if ($this->filterStatus !== '') {
            $query->where('is_active', (bool) $this->filterStatus);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }
}
