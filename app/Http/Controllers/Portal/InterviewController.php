<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\CandidateInterview;
use App\Models\RecruitmentApplication;
use App\Models\InterviewerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    private function checkIsInterviewer(): void
    {
        $user = Auth::user();
        if (!$user->isInterviewer()) {
            abort(403, 'Hanya staf yang memiliki hak akses Interviewer, divisi PND, divisi IE, atau Admin yang dapat mengakses modul ini.');
        }
    }

    /**
     * Tampilkan modul interview:
     * - Jika user belum memiliki role interviewer (dan bukan PND/IE/Admin), tampilkan halaman form pengajuan role.
     * - Jika user berwenang, tampilkan antrean calon medis dan daftar pengajuan role dari anggota lain.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Jika bukan interviewer, bukan IE, bukan PND, bukan Admin: tampilkan form pengajuan diri
        if (!$user->isInterviewer()) {
            $myApplication = InterviewerApplication::where('user_id', $user->id)->latest()->first();
            return view('portal.interview.request', compact('user', 'myApplication'));
        }

        // Ambil data calon medis langsung dari hasil formulir Recruitment Alta Hospital
        $query = RecruitmentApplication::with(['period', 'latestInterview.interviewer', 'user'])
            ->where('hospital', $user->hospital ?? 'alta');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('ic_name', 'like', "%{$search}%")
                  ->orWhere('cid', 'like', "%{$search}%")
                  ->orWhere('discord_username', 'like', "%{$search}%");
            });
        }

        if ($statusFilter = $request->get('status')) {
            $query->where('status', $statusFilter);
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        // Riwayat evaluasi interview yang telah selesai
        $completedInterviews = CandidateInterview::with(['application', 'candidate', 'interviewer'])
            ->latest('interviewed_at')
            ->paginate(15, ['*'], 'completed_page');

        // Hak kelola penugasan interviewer (khusus PND, IE, & Admin)
        $canManageInterviewers = $user->isAdmin() || $user->isInDivision('ie', 'pnd');

        // Daftar pengajuan role interviewer yang menunggu persetujuan (ACC) dari anggota lain
        $pendingApplications = $canManageInterviewers
            ? InterviewerApplication::with(['user.role', 'user.medicRole', 'user.subRole'])
                ->where('hospital', $user->hospital ?? 'alta')
                ->where('status', 'pending')
                ->latest()
                ->get()
            : collect();

        // Riwayat keputusan pengajuan role interviewer terakhir
        $recentDecisions = $canManageInterviewers
            ? InterviewerApplication::with(['user', 'actionBy'])
                ->where('hospital', $user->hospital ?? 'alta')
                ->whereIn('status', ['approved', 'rejected'])
                ->latest('action_at')
                ->take(8)
                ->get()
            : collect();

        // Daftar staf aktif yang saat ini memegang tugas interviewer sementara
        $activeInterviewers = User::with(['role', 'medicRole', 'subRole'])
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_interviewer', true)
            ->get();

        // Staf aktif untuk opsi assign manual
        $availableStaff = $canManageInterviewers
            ? User::with(['role', 'medicRole', 'subRole'])
                ->where('hospital', $user->hospital ?? 'alta')
                ->where('is_active', true)
                ->where('is_interviewer', false)
                ->orderBy('name')
                ->get()
            : collect();

        return view('portal.interview.index', compact(
            'candidates',
            'completedInterviews',
            'pendingApplications',
            'recentDecisions',
            'activeInterviewers',
            'availableStaff',
            'canManageInterviewers'
        ));
    }

    /**
     * Tampilkan formulir pengajuan role interviewer untuk anggota staf
     */
    public function showApplicationForm()
    {
        $user = Auth::user();
        $myApplication = InterviewerApplication::where('user_id', $user->id)->latest()->first();
        return view('portal.interview.request', compact('user', 'myApplication'));
    }

    /**
     * Staf mengajukan diri untuk mendapatkan Role Interviewer sementara
     */
    public function submitApplication(Request $request)
    {
        $user = Auth::user();

        if ($user->is_interviewer) {
            return back()->with('info', 'Anda saat ini sudah memiliki wewenang sebagai Petugas Interviewer aktif.');
        }

        $existingPending = InterviewerApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return back()->with('info', 'Pengajuan Anda sebelumnya masih dalam antrean peninjauan oleh tim PND / IE.');
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        InterviewerApplication::create([
            'user_id'  => $user->id,
            'hospital' => $user->hospital ?? 'alta',
            'reason'   => $validated['reason'] ?? 'Mengajukan diri untuk membantu wawancara calon staf medis pada periode recruitment ini.',
            'status'   => 'pending',
        ]);

        return back()->with('success', 'Pengajuan role Interviewer berhasil dikirim! Menunggu persetujuan (ACC) dari tim PND atau IE.');
    }

    /**
     * PND / IE menyetujui (ACC) pengajuan role interviewer dari staf
     */
    public function approveApplication(InterviewerApplication $application)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isInDivision('ie', 'pnd')) {
            abort(403, 'Hanya divisi IE, PND, atau Admin yang dapat menyetujui pengajuan role Interviewer.');
        }

        $application->update([
            'status'    => 'approved',
            'action_by' => $user->id,
            'action_at' => now(),
        ]);

        // Otomatis aktifkan status interviewer pada user
        $application->user->update([
            'is_interviewer' => true,
        ]);

        return back()->with('success', "Pengajuan untuk {$application->user->name} berhasil di-ACC! Staf otomatis mendapatkan hak akses Interviewer.");
    }

    /**
     * PND / IE menolak pengajuan role interviewer
     */
    public function rejectApplication(Request $request, InterviewerApplication $application)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isInDivision('ie', 'pnd')) {
            abort(403, 'Hanya divisi IE, PND, atau Admin yang dapat menolak pengajuan role Interviewer.');
        }

        $application->update([
            'status'       => 'rejected',
            'action_by'    => $user->id,
            'action_at'    => now(),
            'action_notes' => $request->notes ?? 'Belum memenuhi kualifikasi atau kuota interviewer saat ini telah mencukupi.',
        ]);

        return back()->with('success', "Pengajuan role interviewer untuk {$application->user->name} telah ditolak.");
    }

    /**
     * Tampilkan form interview untuk calon tertentu berdasarkan pendaftaran recruitment
     */
    public function showForm(RecruitmentApplication $candidate)
    {
        $this->checkIsInterviewer();

        $candidate->load(['period', 'candidateInterviews.interviewer', 'user']);

        $allowedRoles = [
            'trainee'      => 'Trainee',
            'perawat'      => 'Perawat',
            'co_ass'       => 'Co-Ass',
            'dokter_umum'  => 'Dokter Umum',
        ];

        return view('portal.interview.evaluate', compact('candidate', 'allowedRoles'));
    }

    /**
     * Simpan hasil evaluasi interview calon medis
     */
    public function storeEvaluation(Request $request, RecruitmentApplication $candidate)
    {
        $this->checkIsInterviewer();

        $validated = $request->validate([
            'result'           => 'required|in:recommended,not_recommended',
            'recommended_role' => 'nullable|required_if:result,recommended|in:trainee,perawat,co_ass,dokter_umum',
            'notes'            => 'required|string|max:2000',
        ], [
            'recommended_role.required_if' => 'Jika memilih Recommended, Anda wajib menentukan rekomendasi jenjang jabatan awal untuk calon staf.',
            'notes.required' => 'Catatan penilaian wawancara wajib diisi.',
        ]);

        $interview = CandidateInterview::create([
            'recruitment_application_id' => $candidate->id,
            'user_id'                    => $candidate->user_id,
            'interviewer_id'             => Auth::id(),
            'result'                     => $validated['result'],
            'recommended_role'           => $validated['result'] === 'recommended' ? $validated['recommended_role'] : null,
            'notes'                      => $validated['notes'],
            'interviewed_at'             => now(),
        ]);

        $roleLabels = [
            'trainee'     => 'Trainee',
            'perawat'     => 'Perawat',
            'co_ass'      => 'Co-Ass',
            'dokter_umum' => 'Dokter Umum',
        ];
        $roleLabel = $roleLabels[$validated['recommended_role'] ?? ''] ?? 'Staf';

        if ($validated['result'] === 'recommended') {
            $candidate->update([
                'status'         => 'interview',
                'reviewer_notes' => "Lolos Wawancara. Direkomendasikan sebagai {$roleLabel} oleh " . Auth::user()->name . ". Catatan: " . $validated['notes'],
            ]);
        } else {
            $candidate->update([
                'status'         => 'rejected',
                'reviewer_notes' => "Tidak lolos wawancara oleh " . Auth::user()->name . ". Catatan: " . $validated['notes'],
            ]);
        }

        // Sinkronisasi ke akun pengguna jika ada yang cocok dengan CID atau user_id
        if ($validated['result'] === 'recommended' && $validated['recommended_role']) {
            $targetUser = $candidate->user ?? User::where('citizen_id', $candidate->cid)->first();
            if ($targetUser) {
                $targetRole = StaffRole::where('name', $validated['recommended_role'])->first();
                if ($targetRole) {
                    $targetUser->update([
                        'role_id'       => $targetRole->id,
                        'medic_role_id' => $targetRole->id,
                    ]);
                }
            }
        }

        return redirect()->route('portal.interview.index')
            ->with('success', "Hasil wawancara untuk calon {$candidate->ic_name} (CID: {$candidate->cid}) berhasil disimpan.");
    }

    /**
     * Penugasan staf langsung sebagai Interviewer Sementara (Khusus PND, IE, atau Admin)
     */
    public function assignInterviewer(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isInDivision('ie', 'pnd')) {
            abort(403, 'Hanya divisi IE, PND, atau Admin yang dapat menugaskan Interviewer sementara.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Pilih staf yang akan ditugaskan sebagai interviewer.',
        ]);

        $target = User::findOrFail($validated['user_id']);
        $target->update(['is_interviewer' => true]);

        return back()->with('success', "Staf {$target->name} berhasil ditugaskan sebagai Petugas Interviewer sementara.");
    }

    /**
     * Cabut penugasan staf sebagai Interviewer Sementara (Khusus PND, IE, atau Admin)
     */
    public function revokeInterviewer(User $user)
    {
        $authUser = Auth::user();
        if (!$authUser->isAdmin() && !$authUser->isInDivision('ie', 'pnd')) {
            abort(403, 'Hanya divisi IE, PND, atau Admin yang dapat mencabut tugas Interviewer.');
        }

        $user->update(['is_interviewer' => false]);

        // Tandai permohonan sebagai revoked
        InterviewerApplication::where('user_id', $user->id)
            ->where('status', 'approved')
            ->update(['status' => 'revoked']);

        return back()->with('success', "Penugasan Interviewer sementara untuk {$user->name} berhasil dicabut.");
    }
}
