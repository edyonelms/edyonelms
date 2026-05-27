<?php

namespace App\Services;

use App\Models\User;

/**
 * Sends activity push-notifications to super-admin users.
 *
 * Fails open: if Firebase isn't configured (no service account / no tokens),
 * it logs and returns without throwing, so the originating action is never
 * interrupted by a notification problem.
 */
class ActivityNotifier
{
    public static function toSuperAdmins(string $title, string $body, array $data = []): void
    {
        try {
            $service = app(FirebaseNotificationService::class);
        } catch (\Throwable $e) {
            // Firebase credentials missing/invalid — skip silently.
            logger()->warning('ActivityNotifier: Firebase unavailable, skipping push', [
                'error' => $e->getMessage(),
            ]);
            return;
        }

        // FCM data payload values must be strings.
        $data = array_map(fn($v) => is_scalar($v) ? (string) $v : '', $data);

        User::whereIn('role', ['super-admin', 'sub-super-admin'])
            ->get()
            ->each(function (User $user) use ($service, $title, $body, $data) {
                try {
                    $service->sendToUser($user, $title, $body, $data);
                } catch (\Throwable $e) {
                    report($e);
                }
            });
    }
}
