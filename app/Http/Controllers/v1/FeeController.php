<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Admin\Fee\FeeStructure;
use App\Models\Student\StudentDetail;
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
}
