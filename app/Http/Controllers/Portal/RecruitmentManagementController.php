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

        $batchName = $application->period?->batch_name;

        // 1. Jika DITERIMA (Accepted): Otomatis aktifkan akun dan sematkan Batch
        if ($request->status === 'accepted') {
            $user = $application->user ?? User::where('citizen_id', $application->cid)->first();
            if ($user) {
                $userUpdates = ['is_active' => true];
                if (!empty($batchName)) {
                    $userUpdates['batch'] = $batchName;
                }
                $user->update($userUpdates);
                if (!$application->user_id) {
                    $application->update(['user_id' => $user->id]);
                }
            } else {
                $traineeRole = StaffRole::where('name', 'trainee')->first()
                    ?? StaffRole::orderBy('level', 'asc')->first();
                $dummyEmail = Str::slug($application->ic_name, '') . rand(100, 999) . '@medic.alta';
                $user = User::create([
                    'name'       => $application->ic_name,
                    'email'      => $dummyEmail,
                    'citizen_id' => $application->cid,
                    'staff_id'   => $application->cid,
                    'password'   => Hash::make(Str::random(10)),
                    'role_id'    => $traineeRole?->id,
                    'hospital'   => 'alta',
                    'batch'      => $batchName,
                    'is_active'  => true,
                ]);
                $application->update(['user_id' => $user->id]);
            }
        } 
        // 2. Jika DITOLAK (Rejected): Otomatis hapus akun sementara agar tidak bisa login
        elseif ($request->status === 'rejected') {
            $user = $application->user ?? User::where('citizen_id', $application->cid)->first();
            if ($user && !$user->isAdmin()) {
                if ($user->role?->name === 'trainee' || !$user->is_active) {
                    $user->delete();
                    $application->update(['user_id' => null]);
                }
            }
        }

        return back()->with('success', "Status pelamar {$application->ic_name} berhasil diperbarui menjadi: " . strtoupper($request->status));
    }

    /**
     * Konversi pelamar ke akun Calon Medis agar masuk ke antrian Interviewer.
     */
    public function convertCandidate(Request $request, RecruitmentApplication $application)
    {
        $this->authorizeManager();

        $batchName = $application->period?->batch_name;

        // Cek apakah sudah punya akun dengan CID ini
        $existing = User::where('citizen_id', $application->cid)->first();

        if ($existing) {
            $updates = [];
            if (!empty($batchName) && empty($existing->batch)) {
                $updates['batch'] = $batchName;
            }
            if (!empty($updates)) {
                $existing->update($updates);
            }
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
            'staff_id'   => $application->cid,
            'password'   => Hash::make($randomPass),
            'role_id'    => $traineeRole?->id,
            'hospital'   => 'alta',
            'batch'      => $batchName,
            'is_active'  => false, // Calon medis non-aktif sebelum interview
        ]);

        $application->update([
            'user_id' => $user->id,
            'status'  => 'interview',
        ]);

        return back()->with('success', "Akun calon medis {$user->name} (#{$user->citizen_id}) berhasil dibuat dan langsung masuk ke Antrian Interviewer Calon Medis!");
    }

    /**
     * Sinkronkan badge Batch untuk anggota yang sudah terdaftar dari periode rekrutmen sebelumnya.
     */
    public function syncBatches()
    {
        $this->authorizeManager();

        $updatedCount = 0;
        RecruitmentApplication::where('status', 'accepted')
            ->whereNotNull('period_id')
            ->with(['period:id,batch_name', 'user:id,citizen_id,batch'])
            ->chunkById(100, function ($applications) use (&$updatedCount) {
                foreach ($applications as $app) {
                    $batchName = $app->period?->batch_name;
                    if (!$batchName) continue;

                    $user = $app->user;
                    if (!$user && !empty($app->cid)) {
                        $user = User::where('citizen_id', $app->cid)->first();
                    }

                    if ($user && empty($user->batch)) {
                        $user->update(['batch' => $batchName]);
                        if (!$app->user_id) {
                            $app->update(['user_id' => $user->id]);
                        }
                        $updatedCount++;
                    }
                }
            });

        return back()->with('success', "Berhasil menyinkronkan {$updatedCount} akun anggota dengan badge batch rekrutmen.");
    }

    /**
     * Hapus satu berkas pendaftaran calon medis beserta file fisiknya.
     */
    public function destroy(RecruitmentApplication $application)
    {
        $this->authorizeManager();

        $name = $application->ic_name;
        \App\Models\CandidateInterview::where('recruitment_application_id', $application->id)
            ->update(['recruitment_application_id' => null]);

        $this->deleteApplicationFiles($application);
        $application->delete();

        return back()->with('success', "Data berkas pendaftaran calon medis '{$name}' berhasil dihapus.");
    }

    /**
     * Bersihkan / reset nama-nama pendaftar rekrutmen (opsi saat pendaftaran ditutup atau reset batch).
     */
    public function clearCandidates(Request $request)
    {
        $this->authorizeManager();

        $scope = $request->input('scope', 'all');

        $query = RecruitmentApplication::where('hospital', 'alta');

        if ($scope === 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($scope === 'pending') {
            $query->where('status', 'pending');
        } elseif ($scope === 'without_interview') {
            $query->whereIn('status', ['pending', 'reviewed', 'rejected']);
        }

        $applications = $query->get();
        $count = $applications->count();

        if ($count === 0) {
            return back()->with('info', 'Tidak ada data pendaftaran calon medis yang sesuai untuk dibersihkan.');
        }

        foreach ($applications as $app) {
            \App\Models\CandidateInterview::where('recruitment_application_id', $app->id)
                ->update(['recruitment_application_id' => null]);
            $this->deleteApplicationFiles($app);
            $app->delete();
        }

        return back()->with('success', "Berhasil membersihkan {$count} data pendaftaran calon medis EMS.");
    }

    /**
     * Helper untuk menghapus file fisik berkas pendaftaran calon.
     */
    private function deleteApplicationFiles(RecruitmentApplication $application): void
    {
        $files = [
            $application->ktp_file,
            $application->skb_file,
            $application->health_cert_file,
            $application->psychology_cert_file,
        ];

        foreach ($files as $filePath) {
            if ($filePath && file_exists(public_path($filePath))) {
                @unlink(public_path($filePath));
            }
        }
    }
}
