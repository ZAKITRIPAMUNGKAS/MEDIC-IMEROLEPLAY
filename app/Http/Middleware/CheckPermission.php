<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support pipe-separated permissions (OR logic)
        // e.g., 'view_reports|view_attendance_reports' means user needs ANY of these
        $permissions = explode('|', $permission);

        foreach ($permissions as $perm) {
            if ($user->hasPermission(trim($perm))) {
                return $next($request);
            }
        }

        // If not authorized, redirect back with error message
        return redirect()->route('staff.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}