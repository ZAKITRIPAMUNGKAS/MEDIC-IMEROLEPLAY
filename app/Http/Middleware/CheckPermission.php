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

        // Cek apakah user memiliki peran dan izin yang sesuai
        if ($user->role && $user->role->hasPermission($permission)) {
            return $next($request);
        }

        // Jika tidak punya izin, kembalikan ke dashboard atau halaman lain dengan pesan error
        return redirect()->route('staff.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}