<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\RecruitmentApplication;
use App\Models\RecruitmentPeriod;
use App\Models\User;
use App\Models\StaffRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RecruitmentManagementController extends Controller
{
    /**
     * Memastikan hanya PND, IE, Executive, atau Admin yang dapat mengakses.
     */
    private function authorizeManager()
    {
        $user = auth()->user();
        $allowed = $user && (
            $user->isAdmin() ||
            $user->isExecutiveOrAbove() ||
            $user->isInDivision('ie') ||
            $user->isInDivision('pnd')
        );

        if (!$allowed) {
            abort(403, 'Akses ditolak. Fitur pengelolaan rekrutmen hanya untuk divisi IE dan PND.');
        }
    }

    /**
     * Halaman Utama Pengelolaan Rekrutmen (Buka/Tutup & Daftar Pelamar).
     */
    public function index(Request $request)
    {
        $this->authorizeManager();

        $currentPeriod = RecruitmentPeriod::currentOpen('alta');
        $allPeriods    = RecruitmentPeriod::forHospital('alta')->latest()->get();

        $query = RecruitmentApplication::where('hospital', 'alta')->with(['period', 'reviewedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ic_name', 'like', "%{$search}%")
                  ->orWhere('cid', 'like', "%{$search}%")
                  ->orWhere('discord_username', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        // Statistik
        $stats = [
            'total'     => RecruitmentApplication::where('hospital', 'alta')->count(),
            'pending'   => RecruitmentApplication::where('hospital', 'alta')->where('status', 'pending')->count(),
            'reviewed'  => RecruitmentApplication::where('hospital', 'alta')->where('status', 'reviewed')->count(),
            'interview' => RecruitmentApplication::where('hospital', 'alta')->where('status', 'interview')->count(),
            'accepted'  => RecruitmentApplication::where('hospital', 'alta')->where('status', 'accepted')->count(),
            'rejected'  => RecruitmentApplication::where('hospital', 'alta')->where('status', 'rejected')->count(),
        ];

        return view('portal.recruitment.manage', compact('currentPeriod', 'allPeriods', 'applications', 'stats'));
    }

    /**
     * Toggle Buka / Tutup Rekrutmen Alta.
     */
    public function toggle(Request $request)
    {
        $this->authorizeManager();

        $current = RecruitmentPeriod::currentOpen('alta');

        if ($current) {
            // Tutup recruitment
            $current->update([
                'is_open'   => false,
                'closed_by' => auth()->id(),
                'closed_at' => now(),
            ]);

            return redirect()->route('portal.recruitment.index')
                ->with('success', "Pendaftaran rekrutmen '{$current->batch_name}' telah berhasil DITUTUP. Banner pengumuman di halaman utama telah dinonaktifkan.");
        } else {
            // Buka recruitment baru
            $batchName = $request->input('batch_name') ?: ('Batch ' . now()->translatedFormat('F Y'));
            $notes     = $request->input('notes');

            RecruitmentPeriod::create([
                'hospital'   => 'alta',
                'batch_name' => $batchName,
                'is_open'    => true,
                'opened_by'  => auth()->id(),
                'opened_at'  => now(),
                'notes'      => $notes,
            ]);

            return redirect()->route('portal.recruitment.index')
                ->with('success', "🎉 Pendaftaran rekrutmen '{$batchName}' BERHASIL DIBUKA! Informasi resmi pendaftaran kini aktif di halaman utama.");
        }
    }

    /**
     * Tampilkan detail pelamar IC, OOC, dan berkas.
     */
    public function show(RecruitmentApplication $application)
    {
        $this->authorizeManager();

        return view('portal.recruitment.show', compact('application'));
    }

    /**
     * Update status pelamar (Reviewed, Interview, Accepted, Rejected).
     */
    public function updateStatus(Request $request, RecruitmentApplication $application)
    {
        $this->authorizeManager();

        $request->validate([
            'status'         => 'required|in:pending,reviewed,interview,accepted,rejected',
            'reviewer_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status'         => $request->status,
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
            'reviewer_notes' => $request->reviewer_notes,
        ]);

        return back()->with('success', "Status pelamar {$application->ic_name} berhasil diperbarui menjadi: " . strtoupper($request->status));
    }

    /**
     * Konversi pelamar ke akun Calon Medis agar masuk ke antrian Interviewer.
     */
    public function convertCandidate(Request $request, RecruitmentApplication $application)
    {
        $this->authorizeManager();

        // Cek apakah sudah punya akun dengan CID ini
        $existing = User::where('citizen_id', $application->cid)->first();

        if ($existing) {
            $application->update(['user_id' => $existing->id]);
            return back()->with('info', "Calon sudah terhubung dengan akun yang ada: {$existing->name} (#{$existing->citizen_id}).");
        }

        // Ambil role trainee (default)
        $traineeRole = StaffRole::where('name', 'trainee')->first()
            ?? StaffRole::orderBy('level', 'asc')->first();

        // Buat akun baru dalam status is_active = false agar masuk antrian interview
        $username = Str::slug($application->ic_name, '.') . rand(10, 99);
        $dummyEmail = Str::slug($application->ic_name, '') . rand(100, 999) . '@medic.alta';
        $randomPass = Str::random(10);

        $user = User::create([
            'name'       => $application->ic_name,
            'email'      => $dummyEmail,
            'citizen_id' => $application->cid,
            'password'   => Hash::make($randomPass),
            'role_id'    => $traineeRole?->id,
            'hospital'   => 'alta',
            'is_active'  => false, // Calon medis non-aktif sebelum interview
        ]);

        $application->update([
            'user_id' => $user->id,
            'status'  => 'interview',
        ]);

        return back()->with('success', "Akun calon medis {$user->name} (#{$user->citizen_id}) berhasil dibuat dan langsung masuk ke Antrian Interviewer Calon Medis!");
    }
}
