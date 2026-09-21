<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\CandidateInterview;
use App\Models\RecruitmentApplication;
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
     * Tampilkan antrean calon medis yang mendaftar melalui recruitment Alta Hospital
     */
    public function index(Request $request)
    {
        $this->checkIsInterviewer();
        $user = Auth::user();

        // Ambil data calon medis langsung dari hasil pengajuan formulir Recruitment Alta Hospital
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

        // Daftar riwayat evaluasi interview yang telah dilakukan
        $completedInterviews = CandidateInterview::with(['application', 'candidate', 'interviewer'])
            ->latest('interviewed_at')
            ->paginate(15, ['*'], 'completed_page');

        // Hak kelola penugasan interviewer sementara (khusus IE, PND, & Admin)
        $canManageInterviewers = $user->isAdmin() || $user->isInDivision('ie', 'pnd');

        // Daftar staf aktif yang sedang ditugaskan sebagai interviewer sementara
        $activeInterviewers = User::with(['role', 'medicRole', 'subRole'])
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_interviewer', true)
            ->get();

        // Daftar staf aktif yang belum ditugaskan untuk dropdown assign
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
            'activeInterviewers',
            'availableStaff',
            'canManageInterviewers'
        ));
    }

    /**
     * Tampilkan form interview untuk calon tertentu berdasarkan pengajuan recruitment
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
     * Simpan hasil interview calon medis
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
     * Penugasan staf sebagai Interviewer Sementara (Hanya PND, IE, atau Admin)
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
     * Cabut penugasan staf sebagai Interviewer Sementara (Hanya PND, IE, atau Admin)
     */
    public function revokeInterviewer(User $user)
    {
        $authUser = Auth::user();
        if (!$authUser->isAdmin() && !$authUser->isInDivision('ie', 'pnd')) {
            abort(403, 'Hanya divisi IE, PND, atau Admin yang dapat mencabut tugas Interviewer.');
        }

        $user->update(['is_interviewer' => false]);

        return back()->with('success', "Penugasan Interviewer sementara untuk {$user->name} berhasil dicabut.");
    }
}
