<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Fee\FeeConcession;
use App\Models\Admin\Fee\FeeCycle;
use App\Models\Admin\Fee\FeePayment;
use App\Models\Admin\Fee\FeeSettings;
use App\Models\Admin\Fee\FeeStructure;
use App\Models\Student\StudentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeController extends ApiController
{
    /**
     * GET /api/v1/fees/summary
     *
     * Student's overall fee summary: total due, paid, remaining, overdue count.
     * Only students (role=user) can access their own summary.
     */
    public function summary()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        if ($err = $this->requireRole('user')) return $err;

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return $this->error('Student profile not found.', 404);
        }

        $orgId     = $user->organization_id;
        $stdId     = $student->standard_id;
        $secId     = $student->section_id;

        // Fee structures applicable to this student's class
        $structures = FeeStructure::where('organization_id', $orgId)
            ->where('standard_id', $stdId)
            ->where(fn($q) => $q->whereNull('section_id')->orWhere('section_id', $secId))
            ->where('is_active', true)
            ->get();

        $totalDue = $structures->sum('amount');

        // What has been paid
        $paid = FeePayment::forStudent($student->id)
            ->forOrg($orgId)
            ->sum('amount');

        $waived = FeePayment::forStudent($student->id)
            ->forOrg($orgId)
            ->sum('waiver_amount');

        $penalties = FeePayment::forStudent($student->id)
            ->forOrg($orgId)
            ->sum('penalty_amount');

        $remaining = max(0, $totalDue - $paid - $waived);

        return $this->success([
            'total_due'         => (float) $totalDue,
            'total_paid'        => (float) $paid,
            'total_waived'      => (float) $waived,
            'total_penalties'   => (float) $penalties,
            'remaining'         => (float) $remaining,
            'academic_due'      => (float) $structures->where('fee_type', 'academic')->sum('amount'),
            'transport_due'     => (float) $structures->where('fee_type', 'transport')->sum('amount'),
        ], 'Fee summary fetched successfully.');
    }

    /**
     * GET /api/v1/fees/structure
     *
     * Fee structures for the student's class (all items and amounts).
     */
    public function structure()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        if ($err = $this->requireRole('user')) return $err;

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return $this->error('Student profile not found.', 404);
        }

        $structures = FeeStructure::where('organization_id', $user->organization_id)
            ->where('standard_id', $student->standard_id)
            ->where(fn($q) => $q->whereNull('section_id')->orWhere('section_id', $student->section_id))
            ->where('is_active', true)
            ->orderBy('fee_type')
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'fee_name'      => $s->fee_name,
                'amount'        => (float) $s->amount,
                'fee_type'      => $s->fee_type,
                'academic_year' => $s->academic_year,
            ]);

        return $this->success($structures, 'Fee structure fetched successfully.');
    }

    /**
     * GET /api/v1/fees/payments
     *
     * List of this student's payment transactions.
     * Filters: fee_type (academic|transport), per_page
     */
    public function payments(Request $request)
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        if ($err = $this->requireRole('user')) return $err;

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return $this->error('Student profile not found.', 404);
        }

        $query = FeePayment::with(['standard:id,name', 'section:id,name'])
            ->forStudent($student->id)
            ->forOrg($user->organization_id)
            ->when($request->filled('fee_type'), fn($q) => $q->where('fee_type', $request->fee_type))
            ->latest('payment_date');

        $payments = $query->paginate((int) $request->get('per_page', 20));

        $items = $payments->getCollection()->map(fn($p) => [
            'id'             => $p->id,
            'receipt_number' => $p->receipt_number,
            'fee_type'       => $p->fee_type,
            'amount'         => (float) $p->amount,
            'waiver_amount'  => (float) $p->waiver_amount,
            'penalty_amount' => (float) $p->penalty_amount,
            'payment_mode'   => $p->payment_mode,
            'payment_date'   => $p->payment_date?->format('Y-m-d'),
            'remark'         => $p->remark,
            'class'          => $p->standard?->name . ($p->section ? ' - ' . $p->section->name : ''),
        ]);

        return $this->paginated($items, $this->paginationMeta($payments), 'Payments fetched successfully.');
    }

    /**
     * GET /api/v1/fees/dashboard
     *
     * One-shot screen for the student: overall summary, upcoming installments
     * (from fee cycles, with overdue penalties), and recent payments.
     */
    public function dashboard(Request $request)
    {
        [$student, $err] = $this->resolveStudent();
        if ($err) return $err;

        $orgId   = $student->organization_id;
        $totals  = $this->feeTotals($student);
        $settings = FeeSettings::getForOrg($orgId);

        $upcoming = $this->upcomingInstallments($student, $totals, $settings);

        $recent = FeePayment::with(['standard:id,name', 'section:id,name'])
            ->forStudent($student->id)
            ->forOrg($orgId)
            ->latest('payment_date')
            ->limit(5)
            ->get()
            ->map(fn($p) => $this->formatPayment($p));

        $overdueCount = collect($upcoming)->where('status', 'overdue')->count();
        $clearedPct   = $totals['total_due'] > 0
            ? (int) round($totals['total_paid'] / $totals['total_due'] * 100)
            : 0;

        return $this->success([
            'summary' => array_merge($totals, [
                'cleared_percent' => min(100, $clearedPct),
            ]),
            'upcoming'        => $upcoming,
            'recent_payments' => $recent,
            'counts'          => [
                'overdue_installments' => $overdueCount,
                'total_installments'   => count($upcoming),
            ],
        ], 'Fee dashboard fetched successfully.');
    }

    /**
     * GET /api/v1/fees/academic
     *
     * Full academic fee structure for the student's class, plus the school's
     * penalty policy and how much penalty has been charged on academic fees.
     */
    public function academic(Request $request)
    {
        [$student, $err] = $this->resolveStudent();
        if ($err) return $err;

        $orgId   = $student->organization_id;
        $settings = FeeSettings::getForOrg($orgId);

        $structures = FeeStructure::where('organization_id', $orgId)
            ->where('standard_id', $student->standard_id)
            ->where(fn($q) => $q->whereNull('section_id')->orWhere('section_id', $student->section_id))
            ->where('is_active', true)
            ->where('fee_type', 'academic')
            ->orderBy('fee_name')
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'fee_name'      => $s->fee_name,
                'amount'        => (float) $s->amount,
                'academic_year' => $s->academic_year,
            ]);

        $structureTotal = (float) $structures->sum('amount');
        $concession     = $this->concessionFor($student, 'academic', $structureTotal);

        $paid = (float) FeePayment::forStudent($student->id)->forOrg($orgId)
            ->where('fee_type', 'academic')->sum('amount');

        $penaltyCharged = (float) FeePayment::forStudent($student->id)->forOrg($orgId)
            ->where('fee_type', 'academic')->sum('penalty_amount');

        return $this->success([
            'academic_year' => $structures->first()['academic_year'] ?? null,
            'structures'    => $structures,
            'totals'        => [
                'structure_total' => $structureTotal,
                'concession'      => $concession,
                'net_due'         => max(0, round($structureTotal - $concession, 2)),
                'paid'            => $paid,
                'remaining'       => max(0, round($structureTotal - $concession - $paid, 2)),
            ],
            'penalty' => [
                'per_day'          => (float) $settings->penalty_per_day,
                'due_day_of_month' => (int) $settings->due_day_of_month,
                'cycle_type'       => $settings->cycle_type,
                'charged'          => $penaltyCharged,
            ],
        ], 'Academic fees fetched successfully.');
    }

    /**
     * GET /api/v1/fees/penalties
     *
     * Only the penalties charged to this student, each tied to the payment
     * (receipt) it was levied on.
     */
    public function penalties(Request $request)
    {
        [$student, $err] = $this->resolveStudent();
        if ($err) return $err;

        $orgId    = $student->organization_id;
        $settings = FeeSettings::getForOrg($orgId);

        $items = FeePayment::with(['standard:id,name', 'section:id,name'])
            ->forStudent($student->id)
            ->forOrg($orgId)
            ->where('penalty_amount', '>', 0)
            ->latest('payment_date')
            ->get()
            ->map(fn($p) => [
                'payment_id'     => $p->id,
                'receipt_number' => $p->receipt_number,
                'fee_type'       => $p->fee_type,
                'base_amount'    => (float) $p->amount,
                'penalty_amount' => (float) $p->penalty_amount,
                'payment_mode'   => $p->payment_mode,
                'payment_date'   => $p->payment_date?->format('Y-m-d'),
                'class'          => $p->standard?->name . ($p->section ? ' - ' . $p->section->name : ''),
                'remark'         => $p->remark,
            ]);

        return $this->success([
            'penalty_per_day' => (float) $settings->penalty_per_day,
            'total_penalty'   => (float) $items->sum('penalty_amount'),
            'count'           => $items->count(),
            'items'           => $items->values(),
        ], 'Penalties fetched successfully.');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Resolve the authenticated student (role=user) or an error response.
     * Usage:  [$student, $err] = $this->resolveStudent(); if ($err) return $err;
     */
    private function resolveStudent(): array
    {
        [$user, $err] = $this->authUser();
        if ($err) return [null, $err];

        if ($err = $this->requireRole('user')) return [null, $err];

        $student = StudentDetail::where('user_id', $user->id)
            ->where('organization_id', $user->organization_id)
            ->first();

        if (!$student) {
            return [null, $this->error('Student profile not found.', 404)];
        }

        return [$student, null];
    }

    /**
     * Overall fee totals (academic + transport) for a student.
     *
     * @return array<string,float>
     */
    private function feeTotals(StudentDetail $student): array
    {
        $orgId = $student->organization_id;

        $structures = FeeStructure::where('organization_id', $orgId)
            ->where('standard_id', $student->standard_id)
            ->where(fn($q) => $q->whereNull('section_id')->orWhere('section_id', $student->section_id))
            ->where('is_active', true)
            ->get();

        $academicDue  = (float) $structures->where('fee_type', 'academic')->sum('amount');
        $transportDue = $student->transportation_required
            ? (float) $structures->where('fee_type', 'transport')->sum('amount')
            : 0.0;

        $payments = FeePayment::forStudent($student->id)->forOrg($orgId)->get();

        $academicPaid  = (float) $payments->where('fee_type', 'academic')->sum('amount');
        $transportPaid = (float) $payments->where('fee_type', 'transport')->sum('amount');
        $penalties     = (float) $payments->sum('penalty_amount');
        $waived        = (float) $payments->sum('waiver_amount');

        $concession = $this->concessionFor($student, 'academic', $academicDue)
            + $this->concessionFor($student, 'transport', $transportDue);

        $totalDue  = $academicDue + $transportDue;
        $totalPaid = $academicPaid + $transportPaid;

        return [
            'total_due'       => round($totalDue, 2),
            'total_paid'      => round($totalPaid, 2),
            'remaining'       => round(max(0, $totalDue - $totalPaid - $waived - $concession), 2),
            'academic_due'    => round($academicDue, 2),
            'academic_paid'   => round($academicPaid, 2),
            'transport_due'   => round($transportDue, 2),
            'transport_paid'  => round($transportPaid, 2),
            'total_penalties' => round($penalties, 2),
            'total_waived'    => round($waived, 2),
            'concession'      => round($concession, 2),
        ];
    }

    /**
     * Total concession (₹) applicable to a fee type for the student.
     */
    private function concessionFor(StudentDetail $student, string $feeType, float $base): float
    {
        if ($base <= 0) return 0.0;

        $discount = FeeConcession::where('organization_id', $student->organization_id)
            ->where('student_detail_id', $student->id)
            ->whereIn('fee_type', [$feeType, 'all'])
            ->get()
            ->sum(fn($c) => $c->discountOn($base));

        return round(min($discount, $base), 2);
    }

    /**
     * Build the academic installment schedule from active fee cycles, allocating
     * what's been paid oldest-first and attaching overdue penalties.
     *
     * @param  array<string,float>  $totals
     */
    private function upcomingInstallments(StudentDetail $student, array $totals, FeeSettings $settings): array
    {
        $cycles = FeeCycle::forOrg($student->organization_id)
            ->active()
            ->where('fee_type', 'academic')
            ->orderBy('payment_serial')
            ->get();

        if ($cycles->isEmpty()) return [];

        $base      = max(0, $totals['academic_due'] - $totals['concession']);
        $remaining = $totals['academic_paid'];
        $today     = Carbon::today();
        $schedule  = [];

        foreach ($cycles as $cycle) {
            $instAmt = $cycle->fee_percent > 0
                ? round($base * (float) $cycle->fee_percent / 100, 2)
                : (float) $cycle->amount;

            if ($remaining >= $instAmt) {
                $paidPortion = $instAmt;
                $remaining  -= $instAmt;
            } elseif ($remaining > 0) {
                $paidPortion = $remaining;
                $remaining   = 0;
            } else {
                $paidPortion = 0;
            }

            $outstanding = round($instAmt - $paidPortion, 2);
            $dueDate     = $cycle->due_date ? Carbon::parse($cycle->due_date) : null;

            $penaltyPerDay = (float) ($cycle->penalty_per_day > 0
                ? $cycle->penalty_per_day
                : $settings->penalty_per_day);

            $daysOverdue = 0;
            $penalty     = 0.0;
            $status      = $outstanding <= 0 ? 'paid' : 'due';

            if ($outstanding > 0 && $paidPortion > 0) {
                $status = 'partial';
            }

            if ($outstanding > 0 && $dueDate && $today->gt($dueDate)) {
                $status      = 'overdue';
                $daysOverdue = (int) $dueDate->diffInDays($today);
                $penalty     = round($daysOverdue * $penaltyPerDay, 2);
            }

            $schedule[] = [
                'serial'       => (int) $cycle->payment_serial,
                'label'        => $this->ordinal((int) $cycle->payment_serial) . ' Installment',
                'due_date'     => $dueDate?->format('Y-m-d'),
                'amount'       => $instAmt,
                'paid'         => round($paidPortion, 2),
                'outstanding'  => $outstanding,
                'penalty'      => $penalty,
                'days_overdue' => $daysOverdue,
                'payable'      => round($outstanding + $penalty, 2),
                'status'       => $status,
            ];
        }

        return $schedule;
    }

    private function formatPayment(FeePayment $p): array
    {
        return [
            'id'             => $p->id,
            'receipt_number' => $p->receipt_number,
            'fee_type'       => $p->fee_type,
            'amount'         => (float) $p->amount,
            'penalty_amount' => (float) $p->penalty_amount,
            'waiver_amount'  => (float) $p->waiver_amount,
            'payment_mode'   => $p->payment_mode,
            'payment_date'   => $p->payment_date?->format('Y-m-d'),
            'class'          => $p->standard?->name . ($p->section ? ' - ' . $p->section->name : ''),
            'remark'         => $p->remark,
        ];
    }

    private function ordinal(int $n): string
    {
        $suffix = ['th', 'st', 'nd', 'rd'];
        $mod = $n % 100;
        return $n . ($suffix[($mod - 20) % 10] ?? $suffix[$mod] ?? $suffix[0]);
    }
}
