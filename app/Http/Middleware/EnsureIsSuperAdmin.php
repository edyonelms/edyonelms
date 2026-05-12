<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If not authenticated
        if (!Auth::check()) {
            return $request->route()->named('super-admin.login')
                ? $next($request)
                : redirect()->route('super-admin.login');
        }

        // If authenticated but not a super-admin
        if ($user->role !== 'super-admin') {
            return redirect()->route('admin.quick-links', ['organization' => $user->organization_id]);
        }

        return $next($request);
    }
}
