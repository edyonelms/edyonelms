<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!Auth::check()) {
            return $request->route()->named('admin.login')
                ? $next($request)
                : redirect()->route('admin.login');
        }

        // If authenticated but not an admin
        if ($user->role !== 'admin') {
            return redirect()->route('super-admin.dashboard'); 
        }

        // Check organization for admin users
        if ($user->role === 'admin') {
            if (!$user->organization_id) {
                Auth::logout();
                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'No organization assigned to this admin account.']);
            }

            $organization = $request->route('organization');
            if ($organization != $user->organization_id) {
                return redirect()->route('admin.quick-links', ['organization' => $user->organization_id])
                    ->withErrors(['organization' => 'Invalid organization access.']);
            }
        }

        return $next($request);
    }
}
