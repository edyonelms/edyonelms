<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Admin\Fee\FeePayment;
use App\Models\Organization;
use App\Models\SuperAdmin\CreditQuery;
use App\Models\SuperAdmin\SuperAdminFeePayment;
use App\Models\User;
use App\Models\WebsiteContact;
use App\Models\WebsiteDemo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Reports extends Component
{
    /** '30d' = last 30 days (day-by-day) | 'monthly' = last 12 months */
    public string $range = '30d';

    /** All-time snapshot cards */
    public array $snapshot = [];

    /** Per-period rows (newest first): label, students, teachers, schools, revenue, fees, credit, enquiries */
    public array $rows = [];

    /** Totals across the visible period */
    public array $totals = [];

    /** Peak values used to scale the inline bars */
    public array $peaks = [];

    public function mount(): void
    {
        $this->loadData();
    }

    public function setRange(string $range): void
    {
        $this->range = in_array($range, ['30d', 'monthly'], true) ? $range : '30d';
        $this->loadData();
    }

    private function loadData(): void
    {
        $this->loadSnapshot();
        $this->range === 'monthly' ? $this->loadMonthly() : $this->loadDaily();
    }

    // ── All-time snapshot ───────────────────────────────────────────────────
    private function loadSnapshot(): void
    {
        $this->snapshot = [
            'students' => User::where('role', 'user')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'schools'  => Organization::count(),
            'revenue'  => $this->safeSum(fn() => SuperAdminFeePayment::paid()->sum('amount')),
            'fees'     => $this->safeSum(fn() => FeePayment::sum('amount')),
        ];
    }

    // ── Last 30 days (day-by-day) ───────────────────────────────────────────
    private function loadDaily(): void
    {
        $start = now()->subDays(29)->startOfDay();

        $students  = $this->groupCount(User::where('role', 'user'), 'created_at', 'DATE', $start);
        $teachers  = $this->groupCount(User::where('role', 'teacher'), 'created_at', 'DATE', $start);
        $schools   = $this->groupCount(Organization::query(), 'created_at', 'DATE', $start);
        $revenue   = $this->groupSum($this->revenueQuery(), 'created_at', 'amount', 'DATE', $start);
        $fees      = $this->groupSum(FeePayment::query(), 'payment_date', 'amount', 'DATE', $start);
        $credit    = $this->groupCount(CreditQuery::query(), 'created_at', 'DATE', $start);
        $enquiries = $this->mergeCounts(
            $this->groupCount(WebsiteDemo::query(), 'created_at', 'DATE', $start),
            $this->groupCount(WebsiteContact::query(), 'created_at', 'DATE', $start),
        );

        $rows = [];
        for ($i = 0; $i < 30; $i++) {
            $day = now()->subDays($i)->startOfDay();
            $key = $day->format('Y-m-d');
            $rows[] = $this->buildRow(
                $day->format('d M'),
                $day->format('D'),
                $key,
                compact('students', 'teachers', 'schools', 'revenue', 'fees', 'credit', 'enquiries')
            );
        }

        $this->rows = $rows; // already newest-first
        $this->finalise();
    }

    // ── Last 12 months ──────────────────────────────────────────────────────
    private function loadMonthly(): void
    {
        $start = now()->subMonths(11)->startOfMonth();

        $students  = $this->groupCount(User::where('role', 'user'), 'created_at', 'MONTH', $start);
        $teachers  = $this->groupCount(User::where('role', 'teacher'), 'created_at', 'MONTH', $start);
        $schools   = $this->groupCount(Organization::query(), 'created_at', 'MONTH', $start);
        $revenue   = $this->groupSum($this->revenueQuery(), 'created_at', 'amount', 'MONTH', $start);
        $fees      = $this->groupSum(FeePayment::query(), 'payment_date', 'amount', 'MONTH', $start);
        $credit    = $this->groupCount(CreditQuery::query(), 'created_at', 'MONTH', $start);
        $enquiries = $this->mergeCounts(
            $this->groupCount(WebsiteDemo::query(), 'created_at', 'MONTH', $start),
            $this->groupCount(WebsiteContact::query(), 'created_at', 'MONTH', $start),
        );

        $rows = [];
        for ($i = 0; $i < 12; $i++) {
            $month = now()->subMonths($i)->startOfMonth();
            $key   = $month->format('Y-m');
            $rows[] = $this->buildRow(
                $month->format('M Y'),
                '',
                $key,
                compact('students', 'teachers', 'schools', 'revenue', 'fees', 'credit', 'enquiries')
            );
        }

        $this->rows = $rows; // newest-first
        $this->finalise();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Revenue source: platform fee payments marked paid (guard scope existence). */
    private function revenueQuery()
    {
        return SuperAdminFeePayment::paid();
    }

    /**
     * One grouped COUNT query → ['bucketKey' => count].
     * $unit: 'DATE' (Y-m-d) or 'MONTH' (Y-m).
     */
    private function groupCount($query, string $col, string $unit, Carbon $start): array
    {
        try {
            $expr = $unit === 'MONTH' ? "DATE_FORMAT($col, '%Y-%m')" : "DATE($col)";
            return $query->whereNotNull($col)
                ->where($col, '>=', $start)
                ->selectRaw("$expr as bkt, COUNT(*) as agg")
                ->groupBy('bkt')
                ->pluck('agg', 'bkt')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** One grouped SUM query → ['bucketKey' => sum]. */
    private function groupSum($query, string $col, string $amountCol, string $unit, Carbon $start): array
    {
        try {
            $expr = $unit === 'MONTH' ? "DATE_FORMAT($col, '%Y-%m')" : "DATE($col)";
            return $query->whereNotNull($col)
                ->where($col, '>=', $start)
                ->selectRaw("$expr as bkt, SUM($amountCol) as agg")
                ->groupBy('bkt')
                ->pluck('agg', 'bkt')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function mergeCounts(array $a, array $b): array
    {
        $out = $a;
        foreach ($b as $k => $v) {
            $out[$k] = ($out[$k] ?? 0) + $v;
        }
        return $out;
    }

    private function buildRow(string $label, string $sub, string $key, array $data): array
    {
        return [
            'label'     => $label,
            'sub'       => $sub,
            'students'  => (int)   ($data['students'][$key]  ?? 0),
            'teachers'  => (int)   ($data['teachers'][$key]  ?? 0),
            'schools'   => (int)   ($data['schools'][$key]   ?? 0),
            'revenue'   => (float) ($data['revenue'][$key]   ?? 0),
            'fees'      => (float) ($data['fees'][$key]      ?? 0),
            'credit'    => (int)   ($data['credit'][$key]    ?? 0),
            'enquiries' => (int)   ($data['enquiries'][$key] ?? 0),
        ];
    }

    private function finalise(): void
    {
        $sum = fn(string $k) => array_sum(array_column($this->rows, $k));
        $this->totals = [
            'students'  => $sum('students'),
            'teachers'  => $sum('teachers'),
            'schools'   => $sum('schools'),
            'revenue'   => $sum('revenue'),
            'fees'      => $sum('fees'),
            'credit'    => $sum('credit'),
            'enquiries' => $sum('enquiries'),
        ];
        $this->peaks = [
            'revenue' => max(1, (float) (max(array_column($this->rows, 'revenue') ?: [0]))),
            'fees'    => max(1, (float) (max(array_column($this->rows, 'fees') ?: [0]))),
        ];
    }

    private function safeSum(\Closure $fn): float
    {
        try {
            return (float) ($fn() ?? 0);
        } catch (\Throwable $e) {
            return 0.0;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.super-admin.reports');
    }
}
