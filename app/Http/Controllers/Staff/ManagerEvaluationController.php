<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ManagerEvaluation;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ManagerEvaluationController extends Controller
{
    /**
     * Helper to auto-create manager_evaluations table if missing on cPanel.
     */
    private function ensureTableExists()
    {
        if (!Schema::hasTable('manager_evaluations')) {
            try {
                Schema::create('manager_evaluations', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
                    $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
                    $table->unsignedTinyInteger('rating');
                    $table->string('kategori')->default('Kepemimpinan & Kinerja');
                    $table->text('komentar');
                    $table->boolean('is_anonymous')->default(true);
                    $table->timestamps();
                });
            } catch (\Throwable $e) {
                // Silently continue if DDL permission is missing
            }
        }
    }

    /**
     * Display manager evaluation dashboard & submission form.
     */
    public function index(Request $request)
    {
        $this->ensureTableExists();

        $currentUser = Auth::user();
        
        // Administrator/Executive role level >= 7 or role name admin/executive can view BOTH hospitals
        $canSeeAll = $currentUser->isAdmin() 
            || strtolower($currentUser->role?->name ?? '') === 'admin' 
            || strtolower($currentUser->role?->name ?? '') === 'executive' 
            || ($currentUser->role?->level ?? 0) >= 7;

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
        if (!Schema::hasTable('manager_evaluations')) {
            $managers = User::where('is_active', true)
                ->whereIn('role_id', $managerRoleIds)
                ->with(['role'])
                ->orderBy('name', 'asc')
                ->get();

            $rhUserIds = User::where('is_active', true)
                ->where(function ($query) {
                    $query->where('hospital', 'roxwood')
                        ->orWhere(function ($sq) {
                            $sq->whereRaw('LOWER(staff_id) LIKE ?', ['%rh%'])
                                ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh -%'])
                                ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh-%']);
                        });
                })
                ->pluck('id')
                ->toArray();

            $allAlta = $managers->reject(fn($u) => in_array($u->id, $rhUserIds) || $u->hospital === 'roxwood');
            $allRoxwood = $managers->filter(fn($u) => in_array($u->id, $rhUserIds) || $u->hospital === 'roxwood');

            if ($canSeeAll) {
                $altaManagers = $allAlta;
                $roxwoodManagers = $allRoxwood;
            } elseif ($currentUser->isRoxwood()) {
                $altaManagers = collect([]);
                $roxwoodManagers = $allRoxwood;
            } else {
                $altaManagers = $allAlta;
                $roxwoodManagers = collect([]);
            }

            $selectedManagerId = null;
            $selectedManager = null;
            $reviews = collect([]);

            return view('staff.evaluations.index', compact('managers', 'altaManagers', 'roxwoodManagers', 'selectedManager', 'selectedManagerId', 'reviews', 'categories', 'canSeeAll'));
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

        $allAlta = $managers->reject(fn($u) => in_array($u->id, $rhUserIds) || $u->hospital === 'roxwood');
        $allRoxwood = $managers->filter(fn($u) => in_array($u->id, $rhUserIds) || $u->hospital === 'roxwood');

        // Hospital Scoping Enforcement
        if ($canSeeAll) {
            $altaManagers = $allAlta;
            $roxwoodManagers = $allRoxwood;
            $allowedManagerIds = $managers->pluck('id');
        } elseif ($currentUser->isRoxwood()) {
            $altaManagers = collect([]);
            $roxwoodManagers = $allRoxwood;
            $allowedManagerIds = $roxwoodManagers->pluck('id');
        } else {
            $altaManagers = $allAlta;
            $roxwoodManagers = collect([]);
            $allowedManagerIds = $altaManagers->pluck('id');
        }

        $selectedManagerId = $request->query('manager_id');
        $selectedManager = null;

        if ($selectedManagerId) {
            $selectedManager = $managers->firstWhere('id', $selectedManagerId);
        }

        // Get reviews for allowed managers
        $reviewsQuery = ManagerEvaluation::with(['manager.role', 'evaluator.role'])
            ->whereIn('manager_id', $allowedManagerIds)
            ->orderBy('created_at', 'desc');

        if ($selectedManagerId && $allowedManagerIds->contains($selectedManagerId)) {
            $reviewsQuery->where('manager_id', $selectedManagerId);
        }

        $reviews = $reviewsQuery->paginate(12);

        return view('staff.evaluations.index', compact('managers', 'altaManagers', 'roxwoodManagers', 'selectedManager', 'selectedManagerId', 'reviews', 'categories', 'canSeeAll'));
    }

    /**
     * Store a new anonymous manager evaluation.
     */
    public function store(Request $request)
    {
        $this->ensureTableExists();

        if (!Schema::hasTable('manager_evaluations')) {
            return back()->with('error', 'Tabel evaluasi manajer di database cPanel belum tersedia. Harap hubungi Admin.');
        }

        $request->validate([
            'manager_id' => 'required|exists:users,id',
            'rating'     => 'required|integer|min:1|max:5',
            'kategori'   => 'nullable',
            'komentar'   => 'required|string|min:5|max:2000',
        ], [
            'manager_id.required' => 'Harap pilih Manajer yang ingin Anda beri penilaian.',
            'rating.required'     => 'Harap berikan nilai bintang (1 - 5 bintang).',
            'komentar.required'   => 'Harap isi komentar evaluasi Anda.',
            'komentar.min'        => 'Komentar minimal 5 karakter.',
        ]);

        $currentUser = Auth::user();
        $evaluatorId = $currentUser->id;

        // Check if targeting self
        if ($evaluatorId == $request->manager_id) {
            return back()->with('error', 'Anda tidak dapat memberikan penilaian untuk diri sendiri.');
        }

        // Check hospital scoping for non-admin users
        $canSeeAll = $currentUser->isAdmin() 
            || strtolower($currentUser->role?->name ?? '') === 'admin' 
            || strtolower($currentUser->role?->name ?? '') === 'executive' 
            || ($currentUser->role?->level ?? 0) >= 7;

        if (!$canSeeAll) {
            $targetManager = User::find($request->manager_id);
            if ($targetManager) {
                if ($currentUser->isRoxwood() && !$targetManager->isRoxwood()) {
                    return back()->with('error', 'Staf Roxwood Hospital hanya dapat menilai Manajer dari Roxwood Hospital.');
                }
                if ($currentUser->isAlta() && $targetManager->isRoxwood()) {
                    return back()->with('error', 'Staf EMS Alta Hospital hanya dapat menilai Manajer dari EMS Alta Hospital.');
                }
            }
        }

        $kategoriInput = $request->input('kategori');
        if (is_array($kategoriInput)) {
            $kategoriFormatted = implode(', ', array_filter($kategoriInput));
        } else {
            $kategoriFormatted = trim((string) $kategoriInput);
        }

        if (empty($kategoriFormatted)) {
            $kategoriFormatted = 'General / Kepemimpinan';
        }

        try {
            ManagerEvaluation::create([
                'evaluator_id' => $evaluatorId,
                'manager_id'   => $request->manager_id,
                'rating'       => $request->rating,
                'kategori'     => $kategoriFormatted,
                'komentar'     => $request->komentar,
                'is_anonymous' => true,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan ulasan evaluasi: ' . $e->getMessage());
        }

        return back()->with('success', 'Penilaian dan evaluasi manajer berhasil dikirim secara ANONIM! Identitas Anda terjamin rahasia.');
    }
}
