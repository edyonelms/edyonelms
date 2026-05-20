<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Record last login timestamp on every successful authentication
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            if ($user && \Illuminate\Support\Facades\Schema::hasColumn($user->getTable(), 'last_login_at')) {
                $user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });

        // // Rate limiting for API
        // RateLimiter::for('api', function (Request $request) {
        //     $key = $request->user()?->id ?: $request->ip();

        //     // Stricter limits for suspicious patterns
        //     if ($this->isSuspiciousRequest($request)) {
        //         return Limit::perMinute(10)->by($key)->response(function () {
        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'Too many requests',
        //                 'status_code' => 429
        //             ], 429);
        //         });
        //     }

        //     return Limit::perMinute(60)->by($key);
        // });

        // // Specific limit for auth endpoints
        // RateLimiter::for('login', function (Request $request) {
        //     return Limit::perMinute(5)->by($request->ip());
        // });
    }

    private function isSuspiciousRequest(Request $request): bool
    {
        $suspiciousPatterns = [
            'carto.run.place',
            'union select',
            '<script',
            'base64_decode',
        ];

        $allInput = implode(' ', array_values($request->all()));

        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($allInput, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
