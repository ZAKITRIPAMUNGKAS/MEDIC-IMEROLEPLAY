<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAltaHospital
{
    /**
     * Handle an incoming request.
     * Memastikan hanya anggota Alta Hospital (atau admin) yang dapat mengakses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('staff.login');
        }

        $hospital = strtolower(trim($user->hospital ?? 'alta'));

        // Jika user terdaftar sebagai Roxwood dan bukan superadmin, cegah akses ke portal Alta
        if ($hospital === 'roxwood' && !$user->isAdmin()) {
            abort(403, 'Fitur portal manajemen dan credit score ini khusus untuk anggota Alta Hospital.');
        }

        return $next($request);
    }
}
