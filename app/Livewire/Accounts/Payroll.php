<?php

namespace App\Livewire\Accounts;

use App\Models\Teacher\TeacherDetail;
use App\Models\Student\Standard;
use App\Models\Student\Section;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Payroll extends Component
{
    use WithPagination;

    // ─── Filters ────────────────────────────────────────────────────────────────
    public string $search          = '';
    public string $filterStandard  = '';
    public int    $perPage         = 20;

    // ─── View Modal ─────────────────────────────────────────────────────────────
    public bool   $showViewModal   = false;
    public        $viewTeacher     = null;

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    // ─── Filter Watchers ────────────────────────────────────────────────────────

    public function updatedSearch(): void         { $this->resetPage(); }
    public function updatedFilterStandard(): void { $this->resetPage(); }

    // ─── View ───────────────────────────────────────────────────────────────────

    public function viewTeacher(int $id): void
    {
        $this->viewTeacher = TeacherDetail::with(['user', 'assignedSubjects.subject', 'assignedSubjects.standard', 'assignedSubjects.section'])
            ->where('organization_id', $this->orgId())
            ->find($id);

        if ($this->viewTeacher) {
            $this->showViewModal = true;
        }
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewTeacher   = null;
    }

    // ─── Reset Filters ───────────────────────────────────────────────────────────

    public function resetFilters(): void
    {
        $this->search        = '';
        $this->filterStandard = '';
        $this->resetPage();
    }

    // ─── Render ─────────────────────────────────────────────────────────────────

    public function render()
    {
        $orgId = $this->orgId();

        $standards = Standard::where('organization_id', $orgId)
            ->where('is_active', true)->orderBy('order')->get();

        $query = TeacherDetail::with(['user'])
            ->where('organization_id', $orgId);

        if ($this->search) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            )->orWhere('employee_id', 'like', "%{$this->search}%");
        }

        $teachers = $query->latest()->paginate($this->perPage);

        // Analytics
        $totalTeachers  = TeacherDetail::where('organization_id', $orgId)->count();
        $joinedThisMonth = TeacherDetail::where('organization_id', $orgId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('livewire.accounts.payroll', compact(
            'standards', 'teachers', 'totalTeachers', 'joinedThisMonth'
        ));
    }
}
