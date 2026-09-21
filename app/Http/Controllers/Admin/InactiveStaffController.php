<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InactiveStaffController extends Controller
{
    /**
     * Cek apakah user punya akses (staff manager ke atas / admin).
     */
    private function checkAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        // Admin selalu boleh; role level >= 5 = Staff Manager ke atas
        if (!$user->isAdmin() && !$user->isManagerOrAbove()) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Staff Manager ke atas.');
        }
    }

    /**
     * GET /admin/inactive-staff
     * Tampilkan staf yang tidak pernah duty selama N bulan penuh.
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        // Berapa bulan — default 2, bisa diubah via query param ?months=1|2|3
        $months = (int) $request->get('months', 2);
        if ($months < 1 || $months > 12) {
            $months = 2;
        }

        $since = Carbon::now()->subMonths($months)->startOfDay();

        // Filter hospital opsional
        $hospital = $request->get('hospital', 'all');

        // Filter search nama / staff_id
        $search = trim($request->get('search', ''));

        /*
         * Query inti:
         * Ambil semua user aktif yang punya role (staf terdaftar)
         * DAN tidak punya satu pun sesi attendance yang selesai (clock_out tidak null)
         * dalam rentang waktu $since sampai sekarang.
         */
        $query = User::with('role:id,name,display_name,level')
            ->where('is_active', true)
            ->whereNotNull('role_id')
            ->whereDoesntHave('attendances', function ($q) use ($since) {
                $q->where('clock_in', '>=', $since)
                  ->whereNotNull('clock_out');
            });

        if ($hospital !== 'all') {
            $query->where('hospital', strtolower($hospital));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('staff_id', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Urutkan: role level tertinggi dulu, lalu nama
        $inactiveStaff = $query->with(['attendances' => function ($q) {
                // Ambil satu sesi terakhir kapanpun (untuk tahu kapan terakhir duty)
                $q->whereNotNull('clock_out')
                  ->orderByDesc('clock_in')
                  ->limit(1);
            }])
            ->orderByRoleLevel()
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        // Statistik ringkas
        $totalInactive = User::where('is_active', true)
            ->whereNotNull('role_id')
            ->whereDoesntHave('attendances', function ($q) use ($since) {
                $q->where('clock_in', '>=', $since)->whereNotNull('clock_out');
            })
            ->when($hospital !== 'all', fn($q) => $q->where('hospital', strtolower($hospital)))
            ->count();

        $totalAktif = User::where('is_active', true)
            ->whereNotNull('role_id')
            ->when($hospital !== 'all', fn($q) => $q->where('hospital', strtolower($hospital)))
            ->count();

        // Distribusi per hospital untuk staf tidak aktif
        $perHospital = User::select('hospital', DB::raw('count(*) as total'))
            ->where('is_active', true)
            ->whereNotNull('role_id')
            ->whereDoesntHave('attendances', function ($q) use ($since) {
                $q->where('clock_in', '>=', $since)->whereNotNull('clock_out');
            })
            ->groupBy('hospital')
            ->pluck('total', 'hospital');

        return view('admin.inactive-staff.index', compact(
            'inactiveStaff',
            'totalInactive',
            'totalAktif',
            'perHospital',
            'months',
            'since',
            'hospital',
            'search'
        ));
    }
}
