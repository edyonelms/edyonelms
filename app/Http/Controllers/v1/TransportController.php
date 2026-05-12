<?php

namespace App\Http\Controllers\v1;

use App\Models\Admin\Transportation;
use App\Models\Student\StudentDetail;

class TransportController extends ApiController
{
    /**
     * GET /api/v1/transport/my-route
     *
     * Returns the transport route assigned to the authenticated student.
     * Only students (role=user) can call this.
     */
    public function myRoute()
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

        // Active transport assigned to this student
        $transport = $student->activeTransportation()
            ->with(['driver.user:id,name,image'])
            ->first();

        if (!$transport) {
            return $this->error('No transport route assigned to you.', 404);
        }

        return $this->success($this->formatTransport($transport), 'Transport route fetched successfully.');
    }

    /**
     * GET /api/v1/transport/routes
     *
     * All active routes for the school (teachers or admin can view).
     */
    public function routes()
    {
        [$user, $err] = $this->authUser();
        if ($err) return $err;

        $routes = Transportation::with(['driver.user:id,name,image'])
            ->where('organization_id', $user->organization_id)
            ->where('is_active', true)
            ->get()
            ->map(fn($t) => $this->formatTransport($t));

        return $this->success($routes, 'Transport routes fetched successfully.');
    }

    // ── Private ───────────────────────────────────────────────────────────────

    private function formatTransport(Transportation $t): array
    {
        return [
            'id'              => $t->id,
            'route_name'      => $t->route_name,
            'pickup_location' => $t->pickup_location,
            'drop_location'   => $t->drop_location,
            'stops'           => $t->stops ?? [],
            'monthly_fee'     => (float) $t->monthly_fee,
            'capacity'        => $t->capacity,
            'driver'          => $t->driver ? [
                'id'          => $t->driver->id,
                'name'        => $t->driver->user?->name,
                'phone'       => $t->driver->phone,
                'license_no'  => $t->driver->license_no,
                'vehicle_no'  => $t->driver->vehicle_no,
                'vehicle_type' => $t->driver->vehicle_type,
            ] : null,
        ];
    }
}
