<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Record last login timestamp on every successful authentication
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            if ($user && \Illuminate\Support\Facades\Schema::hasColumn($user->getTable(), 'last_login_at')) {
                $user->forceFill(['last_login_at' => now()])->saveQuietly();
            }
        });

        // Per-email login throttle (6/min). Stops password brute-force
        // against a single account without locking out unrelated users
        // sharing an IP (e.g. school computer labs).
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? 'login:' . sha1($email) : 'login:ip:' . $request->ip();

            return Limit::perMinute(6)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again in a minute.',
                ], 429);
            });
        });

        // OTP / password reset throttle (4/min per email or IP fallback).
        RateLimiter::for('otp', function (Request $request) {
            $email = strtolower((string) $request->input('email', ''));
            $key = $email !== '' ? 'otp:' . sha1($email) : 'otp:ip:' . $request->ip();

            return Limit::perMinute(4)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many OTP requests. Please wait a minute before retrying.',
                ], 429);
            });
        });
    }
}
