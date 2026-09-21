<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffRole;
use App\Models\CandidateInterview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    private function checkIsInterviewer(): void
    {
        $user = Auth::user();
        if (!$user->isInterviewer()) {
            abort(403, 'Hanya staf yang memiliki hak akses Interviewer yang dapat mengakses modul ini.');
        }
    }

    /**
     * Tampilkan antrean calon medis yang belum aktif / siap di-interview
     */
    public function index(Request $request)
    {
        $this->checkIsInterviewer();
        $user = Auth::user();

        // Calon medis adalah user yang belum aktif (is_active = false)
        // atau yang memiliki riwayat interview
        $query = User::with(['role', 'candidateInterviews.interviewer'])
            ->where('hospital', $user->hospital ?? 'alta')
            ->where('is_active', false);

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('citizen_id', 'like', "%{$search}%");
            });
        }

        $candidates = $query->latest()->paginate(20)->withQueryString();

        // Daftar yang sudah di-interview
        $completedInterviews = CandidateInterview::with(['candidate', 'interviewer'])
            ->whereHas('candidate', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest('interviewed_at')
            ->paginate(15, ['*'], 'completed_page');

        return view('portal.interview.index', compact('candidates', 'completedInterviews'));
    }

    /**
     * Tampilkan form interview untuk calon tertentu
     */
    public function showForm(User $candidate)
    {
        $this->checkIsInterviewer();

        $candidate->load(['candidateInterviews.interviewer', 'role']);

        $allowedRoles = [
            'trainee'      => 'Trainee',
            'perawat'      => 'Perawat',
            'co_ass'       => 'Co-Ass',
            'dokter_umum'  => 'Dokter Umum',
        ];

        return view('portal.interview.evaluate', compact('candidate', 'allowedRoles'));
    }

    /**
     * Simpan hasil interview
     */
    public function storeEvaluation(Request $request, User $candidate)
    {
        $this->checkIsInterviewer();

        $validated = $request->validate([
            'result'           => 'required|in:recommended,not_recommended',
            'recommended_role' => 'nullable|required_if:result,recommended|in:trainee,perawat,co_ass,dokter_umum',
            'notes'            => 'required|string|max:2000',
        ], [
            'recommended_role.required_if' => 'Jika memilih Recommended, Anda wajib menentukan rekomendasi jabatan untuk calon staf.',
            'notes.required' => 'Catatan penilaian wawancara wajib diisi.',
        ]);

        $interview = CandidateInterview::create([
            'user_id'          => $candidate->id,
            'interviewer_id'   => Auth::id(),
            'result'           => $validated['result'],
            'recommended_role' => $validated['result'] === 'recommended' ? $validated['recommended_role'] : null,
            'notes'            => $validated['notes'],
            'interviewed_at'   => now(),
        ]);

        // Jika recommended, kita bisa otomatis set role_id ke role yang direkomendasikan jika diinginkan
        if ($validated['result'] === 'recommended' && $validated['recommended_role']) {
            $targetRole = StaffRole::where('name', $validated['recommended_role'])->first();
            if ($targetRole) {
                $candidate->update([
                    'role_id'       => $targetRole->id,
                    'medic_role_id' => $targetRole->id,
                ]);
            }
        }

        return redirect()->route('portal.interview.index')
            ->with('success', "Hasil interview untuk calon {$candidate->name} berhasil disimpan di sistem.");
    }
}
