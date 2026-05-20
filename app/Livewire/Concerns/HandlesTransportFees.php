<?php

namespace App\Livewire\Concerns;

use App\Models\Admin\Transportation;
use App\Models\Admin\TransportFeePayment;
use App\Models\Student\StudentDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Shared transport-fee logic for the Admin & Accounts Transport components.
 *
 * Annual transport fee = route.monthly_fee × 11  (June is excluded).
 *
 * Requires the host component to define:
 *   - protected function txOrgId(): int   (organization id)
 *   - WireUiActions trait (for $this->notification())
 */
trait HandlesTransportFees
{
    // Number of billable months in the year (June excluded)
    public int $billableMonths = 11;

    // ── Fee Summary tab state ────────────────────────────────────────────────
    public string $feeStudentSearch = '';
    public ?int   $feeStudentId     = null;

    // Payment form
    public bool   $showPaymentPanel = false;
    public $payAmount;
    public string $payMode = 'cash';
    public string $payDate = '';
    public string $payRemark = '';

    // Delete payment confirm
    public ?int $pendingDeletePaymentId = null;

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Annual transport fee for a route (monthly × 11). */
    public function annualFee($monthlyFee): float
    {
        return round((float) $monthlyFee * $this->billableMonths, 2);
    }

    /** The route a student is assigned to (first active match). */
    protected function studentRoute(int $studentDetailId): ?Transportation
    {
        return Transportation::where('organization_id', $this->txOrgId())
            ->whereHas('students', fn($q) => $q->where('student_details.id', $studentDetailId))
            ->orderByDesc('is_active')
            ->first();
    }

    // ── Tab 3: Transport Students list ────────────────────────────────────────

    /** Students currently assigned to any route, with fee figures. */
    public function transportStudents()
    {
        $orgId = $this->txOrgId();

        $query = StudentDetail::with(['user:id,name,image', 'standard:id,name', 'section:id,name', 'transportations:id,route_name,monthly_fee'])
            ->where('student_details.organization_id', $orgId)
            ->whereHas('transportations')
            ->when($this->search ?? '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('full_name', 'like', '%' . $this->search . '%')
                       ->orWhere('admission_no', 'like', '%' . $this->search . '%');
                });
            });

        if (!empty($this->filterRoute)) {
            $query->whereHas('transportations', fn($q) => $q->where('transportations.id', $this->filterRoute));
        }

        $students = $query->orderBy('full_name')->paginate($this->perPage ?? 15);

        // Precompute paid totals for the page
        $ids = $students->getCollection()->pluck('id')->all();
        $paidMap = TransportFeePayment::where('organization_id', $orgId)
            ->whereIn('student_detail_id', $ids)
            ->selectRaw('student_detail_id, SUM(amount) as paid')
            ->groupBy('student_detail_id')
            ->pluck('paid', 'student_detail_id');

        $students->getCollection()->transform(function ($s) use ($paidMap) {
            $route = $s->transportations->first();
            $monthly = $route?->monthly_fee ?? 0;
            $annual  = $this->annualFee($monthly);
            $paid    = (float) ($paidMap[$s->id] ?? 0);
            $s->_route     = $route;
            $s->_monthly   = $monthly;
            $s->_annual    = $annual;
            $s->_paid      = $paid;
            $s->_remaining = max(0, $annual - $paid);
            return $s;
        });

        return $students;
    }

    // ── Tab 4: Fee Summary for a single student ─────────────────────────────────

    public function selectFeeStudent(int $id): void
    {
        $this->feeStudentId = $id;
        $this->feeStudentSearch = '';
    }

    public function clearFeeStudent(): void
    {
        $this->feeStudentId = null;
        $this->feeStudentSearch = '';
    }

    /** Search results for the fee-summary student picker. */
    public function feeStudentResults()
    {
        if (strlen($this->feeStudentSearch) < 2) {
            return collect();
        }
        return StudentDetail::with(['standard:id,name', 'section:id,name'])
            ->where('organization_id', $this->txOrgId())
            ->whereHas('transportations')
            ->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->feeStudentSearch . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->feeStudentSearch . '%');
            })
            ->limit(10)->get();
    }

    /** Full fee summary + transactions for the selected student. */
    public function feeSummary(): ?array
    {
        if (!$this->feeStudentId) return null;

        $orgId   = $this->txOrgId();
        $student = StudentDetail::with(['user:id,name,image', 'standard:id,name', 'section:id,name'])
            ->where('organization_id', $orgId)->find($this->feeStudentId);
        if (!$student) return null;

        $route   = $this->studentRoute($student->id);
        $monthly = $route?->monthly_fee ?? 0;
        $annual  = $this->annualFee($monthly);

        $payments = TransportFeePayment::with('transportation:id,route_name')
            ->where('organization_id', $orgId)
            ->where('student_detail_id', $student->id)
            ->orderByDesc('payment_date')->orderByDesc('id')
            ->get();

        $paid = (float) $payments->sum('amount');

        return [
            'student'    => $student,
            'route'      => $route,
            'monthly'    => $monthly,
            'annual'     => $annual,
            'paid'       => $paid,
            'remaining'  => max(0, $annual - $paid),
            'payments'   => $payments,
        ];
    }

    // ── Add payment ──────────────────────────────────────────────────────────

    public function openPaymentPanel(): void
    {
        $this->resetErrorBag();
        $summary = $this->feeSummary();
        $this->payAmount = $summary ? max(0, $summary['remaining']) : null;
        $this->payMode   = 'cash';
        $this->payDate   = now()->toDateString();
        $this->payRemark = '';
        $this->showPaymentPanel = true;
    }

    public function closePaymentPanel(): void
    {
        $this->showPaymentPanel = false;
        $this->payAmount = null;
        $this->payRemark = '';
    }

    public function savePayment(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:1',
            'payMode'   => 'required|in:cash,online,cheque,upi',
            'payDate'   => 'required|date',
            'payRemark' => 'nullable|string|max:255',
        ]);

        if (!$this->feeStudentId) {
            $this->notification()->error('Error', 'Select a student first.');
            return;
        }

        $orgId   = $this->txOrgId();
        $student = StudentDetail::where('organization_id', $orgId)->find($this->feeStudentId);
        if (!$student) {
            $this->notification()->error('Error', 'Student not found.');
            return;
        }
        $route = $this->studentRoute($student->id);

        DB::transaction(function () use ($orgId, $student, $route) {
            TransportFeePayment::create([
                'organization_id'   => $orgId,
                'transportation_id' => $route?->id,
                'student_detail_id' => $student->id,
                'amount'            => $this->payAmount,
                'payment_mode'      => $this->payMode,
                'payment_date'      => $this->payDate,
                'receipt_number'    => $this->generateTransportReceiptNumber($orgId),
                'academic_year'     => now()->format('Y') . '-' . substr((string) (now()->year + 1), -2),
                'remark'            => $this->payRemark ?: null,
                'submitted_by'      => Auth::id(),
            ]);
        });

        $this->notification()->success('Success', 'Transport fee payment recorded.');
        $this->closePaymentPanel();
    }

    private function generateTransportReceiptNumber(int $orgId): string
    {
        $year = now()->format('y');
        $base = "TRP{$orgId}{$year}";
        $last = TransportFeePayment::where('organization_id', $orgId)
            ->where('receipt_number', 'like', "{$base}%")
            ->orderByDesc('id')->first();
        $serial = $last ? ((int) substr($last->receipt_number, -5) + 1) : 1;
        return $base . str_pad((string) $serial, 5, '0', STR_PAD_LEFT);
    }

    public function confirmDeletePayment(int $id): void { $this->pendingDeletePaymentId = $id; }
    public function cancelDeletePayment(): void { $this->pendingDeletePaymentId = null; }
    public function executeDeletePayment(): void
    {
        if ($this->pendingDeletePaymentId) {
            TransportFeePayment::where('id', $this->pendingDeletePaymentId)
                ->where('organization_id', $this->txOrgId())->delete();
            $this->notification()->success('Deleted', 'Payment removed.');
        }
        $this->pendingDeletePaymentId = null;
    }
}
