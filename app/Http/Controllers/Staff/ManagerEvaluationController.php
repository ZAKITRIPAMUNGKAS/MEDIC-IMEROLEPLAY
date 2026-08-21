<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ManagerEvaluation;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerEvaluationController extends Controller
{
    /**
     * Display manager evaluation dashboard & submission form.
     */
    public function index(Request $request)
    {
        // Manager roles are level >= 5 (Staff Manager, Manajer, Executive, Admin)
        $managerRoleIds = StaffRole::where('level', '>=', 5)->pluck('id');

        $categories = [
            'Kepemimpinan & Komunikasi',
            'Sikap & Etika',
            'Keadilan & Pengayoman Staf',
            'Respon & Penyelesaian Masalah',
            'Kinerja & Profesionalisme',
            'Lainnya / General',
        ];

        // Defensive check: If database table has not been migrated yet on production cPanel
        if (!\Illuminate\Support\Facades\Schema::hasTable('manager_evaluations')) {
            $managers = User::where('is_active', true)
                ->whereIn('role_id', $managerRoleIds)
                ->with(['role'])
                ->orderBy('name', 'asc')
                ->get();
            $reviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
            $selectedManager = null;
            $selectedManagerId = null;

            return view('staff.evaluations.index', compact('managers', 'selectedManager', 'selectedManagerId', 'reviews', 'categories'))
                ->with('error', 'Tabel evaluasi manajer belum dibuat di database cPanel hosting. Harap jalankan perintah "php artisan migrate" di cPanel Terminal.');
        }

        $managers = User::where('is_active', true)
            ->whereIn('role_id', $managerRoleIds)
            ->with(['role'])
            ->withCount('evaluationsReceived as reviews_count')
            ->withAvg('evaluationsReceived as avg_rating', 'rating')
            ->orderBy('name', 'asc')
            ->get();

        // Identify Roxwood Hospital (RH) user IDs
        $rhUserIds = User::where('is_active', true)
            ->where(function ($query) {
                $query->where('hospital', 'roxwood')
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%roxwood%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh -%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%rh-%'])
                    ->orWhere(function ($q) {
                        $q->whereNotNull('staff_id')
                            ->where(function ($sq) {
                                $sq->whereRaw('LOWER(staff_id) LIKE ?', ['%rh%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh -%'])
                                    ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh-%']);
                            });
                    });
            })
            ->pluck('id')
            ->toArray();

        $altaManagers = $managers->reject(function ($user) use ($rhUserIds) {
            return in_array($user->id, $rhUserIds) || $user->hospital === 'roxwood';
        });

        $roxwoodManagers = $managers->filter(function ($user) use ($rhUserIds) {
            return in_array($user->id, $rhUserIds) || $user->hospital === 'roxwood';
        });

        $selectedManagerId = $request->query('manager_id');
        $selectedManager = null;

        if ($selectedManagerId) {
            $selectedManager = $managers->firstWhere('id', $selectedManagerId);
        }

        // Get reviews for selected manager or all recent reviews
        $reviewsQuery = ManagerEvaluation::with(['manager.role'])
            ->orderBy('created_at', 'desc');

        if ($selectedManagerId) {
            $reviewsQuery->where('manager_id', $selectedManagerId);
        }

        $reviews = $reviewsQuery->paginate(12);

        return view('staff.evaluations.index', compact('managers', 'altaManagers', 'roxwoodManagers', 'selectedManager', 'selectedManagerId', 'reviews', 'categories'));
    }

    /**
     * Store a new anonymous manager evaluation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'rating'     => 'required|integer|min:1|max:5',
            'kategori'   => 'required|string|max:255',
            'komentar'   => 'required|string|min:5|max:2000',
        ], [
            'manager_id.required' => 'Harap pilih Manajer yang ingin Anda beri penilaian.',
            'rating.required'     => 'Harap berikan nilai bintang (1 - 5 bintang).',
            'komentar.required'   => 'Harap isi komentar evaluasi Anda.',
            'komentar.min'        => 'Komentar minimal 5 karakter.',
        ]);

        $evaluatorId = Auth::id();

        // Check if targeting self
        if ($evaluatorId == $request->manager_id) {
            return back()->with('error', 'Anda tidak dapat memberikan penilaian untuk diri sendiri.');
        }

        ManagerEvaluation::create([
            'evaluator_id' => $evaluatorId,
            'manager_id'   => $request->manager_id,
            'rating'       => $request->rating,
            'kategori'     => $request->kategori,
            'komentar'     => $request->komentar,
            'is_anonymous' => true,
        ]);

        return back()->with('success', 'Penilaian dan evaluasi manajer berhasil dikirim secara ANONIM! Identitas Anda terjamin rahasia.');
    }
}
