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
        $relations = ['period', 'user'];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
                $relations[] = 'latestInterview.interviewer';
            }
        } catch (\Throwable $e) {}

        $query = RecruitmentApplication::with($relations)
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
        $completedInterviews = collect();
        try {
            $completedRelations = ['candidate', 'interviewer'];
            if (\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
                $completedRelations[] = 'application';
            }
            $completedInterviews = CandidateInterview::with($completedRelations)
                ->latest('interviewed_at')
                ->paginate(15, ['*'], 'completed_page');
        } catch (\Throwable $e) {
            $completedInterviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

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

        // Daftar seluruh pendaftar yang lolos wawancara / rekomendasi untuk pengumuman
        $passedCandidates = collect();
        try {
            $passedCandidates = RecruitmentApplication::with(['period'])
                ->where('hospital', $user->hospital ?? 'alta')
                ->where(function ($q) {
                    $q->where('status', 'accepted')
                      ->orWhere('status', 'interview')
                      ->orWhere('reviewer_notes', 'like', '%Lolos%');
                })
                ->latest()
                ->get();
        } catch (\Throwable $e) {
            $passedCandidates = collect();
        }

        // Siapkan format array siap pakai untuk JavaScript pengumuman
        $passedList = $passedCandidates->map(function ($c) {
            $role = 'Staf Medis';
            if (!empty($c->reviewer_notes) && preg_match('/Direkomendasikan sebagai ([^.]+)/i', $c->reviewer_notes, $matches)) {
                $role = trim($matches[1]);
            }
            return [
                'id'       => $c->id,
                'ic_name'  => $c->ic_name,
                'cid'      => $c->cid,
                'discord'  => $c->discord_username ? '@' . ltrim($c->discord_username, '@') : '-',
                'batch'    => $c->period?->batch_name ?? 'Recruitment Batch',
                'role'     => $role,
                'status'   => $c->status,
            ];
        })->values();

        $currentCandidatesList = collect($candidates->items())->map(function ($c) {
            $role = 'Staf Medis';
            if (!empty($c->reviewer_notes) && preg_match('/Direkomendasikan sebagai ([^.]+)/i', $c->reviewer_notes, $matches)) {
                $role = trim($matches[1]);
            }
            return [
                'id'       => $c->id,
                'ic_name'  => $c->ic_name,
                'cid'      => $c->cid,
                'discord'  => $c->discord_username ? '@' . ltrim($c->discord_username, '@') : '-',
                'batch'    => $c->period?->batch_name ?? 'Recruitment Batch',
                'role'     => $role,
                'status'   => $c->status,
            ];
        })->values();

        return view('portal.interview.index', compact(
            'candidates',
            'passedCandidates',
            'passedList',
            'currentCandidatesList',
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

        $targetUser = $candidate->user ?? ($candidate->cid ? User::where('citizen_id', $candidate->cid)->first() : null);
        $candidateUserId = $candidate->user_id ?? ($targetUser ? $targetUser->id : null);

        $interviewData = [
            'recruitment_application_id' => $candidate->id,
            'user_id'                    => $candidateUserId,
            'interviewer_id'             => Auth::id(),
            'result'                     => $validated['result'],
            'recommended_role'           => $validated['result'] === 'recommended' ? $validated['recommended_role'] : null,
            'notes'                      => $validated['notes'],
            'interviewed_at'             => now(),
        ];

        // Safety check if database table column is missing in any environment
        try {
            CandidateInterview::create($interviewData);
        } catch (\Illuminate\Database\QueryException $e) {
            // If user_id is strictly not null in DB legacy schema, fallback to interviewer id
            if ($e->getCode() === '23000' && !$candidateUserId) {
                $interviewData['user_id'] = Auth::id();
                CandidateInterview::create($interviewData);
            } else {
                throw $e;
            }
        }

        $roleLabels = [
            'trainee'     => 'Trainee',
            'perawat'     => 'Perawat',
            'co_ass'      => 'Co-Ass',
            'dokter_umum' => 'Dokter Umum',
        ];
        $roleLabel = $roleLabels[$validated['recommended_role'] ?? ''] ?? 'Staf';

        $batchName = $candidate->period?->batch_name;

        if ($validated['result'] === 'recommended') {
            $candidate->update([
                'status'         => 'accepted',
                'reviewer_notes' => "Lolos Wawancara. Direkomendasikan sebagai {$roleLabel} oleh " . Auth::user()->name . ". Catatan: " . $validated['notes'],
            ]);

            $targetRole = !empty($validated['recommended_role']) 
                ? StaffRole::where('name', $validated['recommended_role'])->first() 
                : (StaffRole::where('name', 'trainee')->first() ?? StaffRole::orderBy('level', 'asc')->first());

            if ($targetUser) {
                $userUpdates = [
                    'is_active' => true,
                ];
                if ($targetRole) {
                    $userUpdates['role_id'] = $targetRole->id;
                    $userUpdates['medic_role_id'] = $targetRole->id;
                }
                if (!empty($batchName)) {
                    $userUpdates['batch'] = $batchName;
                }
                $targetUser->update($userUpdates);
                if (!$candidate->user_id) {
                    $candidate->update(['user_id' => $targetUser->id]);
                }
            } else {
                // Auto create akun aktif langsung
                $dummyEmail = \Illuminate\Support\Str::slug($candidate->ic_name, '') . rand(100, 999) . '@medic.alta';
                $newUser = User::create([
                    'name'          => $candidate->ic_name,
                    'email'         => $dummyEmail,
                    'citizen_id'    => $candidate->cid,
                    'staff_id'      => $candidate->cid,
                    'password'      => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(10)),
                    'role_id'       => $targetRole?->id,
                    'medic_role_id' => $targetRole?->id,
                    'hospital'      => 'alta',
                    'batch'         => $batchName,
                    'is_active'     => true,
                ]);
                $candidate->update(['user_id' => $newUser->id]);
            }
        } else {
            $candidate->update([
                'status'         => 'rejected',
                'reviewer_notes' => "Tidak lolos wawancara oleh " . Auth::user()->name . ". Catatan: " . $validated['notes'],
            ]);

            // Jika tidak lolos, hapus akun sementara agar database bersih dan tidak bisa login
            if ($targetUser && !$targetUser->isAdmin()) {
                if ($targetUser->role?->name === 'trainee' || !$targetUser->is_active) {
                    $targetUser->delete();
                    $candidate->update(['user_id' => null]);
                }
            }
        }

        $successMsg = "Hasil wawancara untuk calon {$candidate->ic_name} (CID: {$candidate->cid}) berhasil disimpan.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'message'      => $successMsg,
                'redirect_url' => route('portal.interview.index'),
            ]);
        }

        return redirect()->route('portal.interview.index')
            ->with('success', $successMsg);
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
