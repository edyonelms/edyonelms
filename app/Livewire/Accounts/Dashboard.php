<?php

namespace App\Livewire\Accounts;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Admin\Fee\FeeStructure;
use App\Models\Student\StudentDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalFeeCollected = 0;
    public $totalPending = 0;
    public $todayCollection = 0;
    public $totalStudents = 0;

    public function mount(): void
    {
        $orgId = $this->orgId();

        $this->totalFeeCollected = FeePayment::where('organization_id', $orgId)->sum('amount');

        $totalStructureAmount = FeeStructure::where('organization_id', $orgId)
            ->where('is_active', true)
            ->sum('amount');

        $this->totalPending = max(0, $totalStructureAmount - $this->totalFeeCollected);

        $this->todayCollection = FeePayment::where('organization_id', $orgId)
            ->whereDate('payment_date', today())
            ->sum('amount');

        $this->totalStudents = StudentDetail::where('organization_id', $orgId)->count();
    }

    private function orgId(): int
    {
        return Auth::user()->organization_id;
    }

    public function render()
    {
        return view('livewire.accounts.dashboard');
    }
}
