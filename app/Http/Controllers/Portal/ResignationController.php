<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ResignationRequest;
use App\Models\ResignationLog;
use App\Models\OrganizationalStructure;
use App\Models\User;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Helpers\PayrollHelper;

class ResignationController extends Controller
{
    // ─── Anggota: Submit Resign ───────────────────────────────────────────────

    public function index()
    {
        $user    = Auth::user();
        $request = ResignationRequest::where('user_id', $user->id)->latest()->first();
        return view('portal.resignation.index', compact('request', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        $managerialPosition = null;

        // Coba ambil jabatan manajerial dari struktur organisasi
        try {
            $managerialPosition = OrganizationalStructure::where('user_id', $user->id)->value('managerial_position');
        } catch (\Throwable $e) {}

        // Cek apakah sudah ada pengajuan resign aktif
        $existingRequest = ResignationRequest::where('user_id', $user->id)
            ->whereNotIn('status', [ResignationRequest::STATUS_COMPLETED, ResignationRequest::STATUS_REJECTED, ResignationRequest::STATUS_CANCELLED])
            ->first();

        if ($existingRequest) {
            return redirect()->route('portal.resignation.index')
                ->with('info', 'Anda sudah memiliki pengajuan resign yang sedang diproses.');
        }

        return view('portal.resignation.create', [
            'user'               => $user,
            'letterDate'         => Carbon::today()->format('d/m/Y'),
            'position'           => $user->role?->display_name ?? '-',
            'managerialPosition' => $managerialPosition ?? '-',
            'batch'              => $user->batch ?? '-',
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Cek double-submit
        $existing = ResignationRequest::where('user_id', $user->id)
            ->whereNotIn('status', [ResignationRequest::STATUS_COMPLETED, ResignationRequest::STATUS_REJECTED, ResignationRequest::STATUS_CANCELLED])
            ->first();
        if ($existing) {
            return redirect()->route('portal.resignation.index')
                ->with('error', 'Pengajuan resign sebelumnya masih dalam proses.');
        }

        $validated = $request->validate([
            'reason_ic'  => 'required|string|max:2000',
            'reason_ooc' => 'required|string|max:2000',
        ]);

        $position = $user->role?->display_name ?? '-';

        // Hitung akumulasi gaji pokok yang telah diterima selama masa kerja (hanya gaji pokok, tanpa bonus)
        $paidBaseSalary = (int) Payroll::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('base_salary');

        if ($paidBaseSalary <= 0) {
            $allBaseSalary = (int) Payroll::where('user_id', $user->id)->sum('base_salary');
            $paidBaseSalary = $allBaseSalary > 0
                ? $allBaseSalary
                : (int) PayrollHelper::getBaseSalary($user->role?->name, $user->custom_salary ?? 0);
        }

        $baseSalary = $paidBaseSalary;

        // Generate standard text
        $standardText = "Dengan hormat,\n\nSaya yang bertanda tangan di bawah ini:\n\nNama : {$user->name}\nJabatan : {$position}\n\nMenyatakan mengundurkan diri dari posisi yang saya emban di Alta Hospital terhitung sejak surat ini dibuat.\n\nSaya mengucapkan terima kasih atas kesempatan dan kepercayaan yang telah diberikan selama ini. Mohon maaf atas segala kesalahan yang pernah terjadi selama saya bertugas.\n\nHormat saya,\n{$user->name}";

        ResignationRequest::create([
            'user_id'            => $user->id,
            'letter_date'        => now()->toDateString(),
            'applicant_name'     => $user->name,
            'position'           => $position,
            'managerial_position'=> $request->managerial_position ?? '-',
            'batch'              => $request->batch ?? '-',
            'reason_ic'          => $validated['reason_ic'],
            'reason_ooc'         => $validated['reason_ooc'],
            'standard_text'      => $standardText,
            'status'             => ResignationRequest::STATUS_PENDING_PND,
            'base_salary'        => $baseSalary,
        ]);

        return redirect()->route('portal.resignation.index')
            ->with('success', 'Permohonan resign berhasil dikirim ke divisi PND untuk diverifikasi.');
    }

    public function cancelOwn(Request $request)
    {
        $user = Auth::user();
        $resignation = ResignationRequest::where('user_id', $user->id)
            ->where('status', ResignationRequest::STATUS_PENDING_PND)
            ->first();

        if (!$resignation) {
            return back()->with('error', 'Tidak ada permohonan resign aktif yang dapat dibatalkan.');
        }

        $resignation->delete();
        $user->update(['is_active' => true]);

        return redirect()->route('portal.resignation.index')
            ->with('success', 'Pengajuan permohonan resign Anda berhasil dibatalkan.');
    }

    public function show(ResignationRequest $resignation)
    {
        $this->authorizeView($resignation);
        return view('portal.resignation.show', compact('resignation'));
    }

    // ─── Anggota: Upload Bukti Resign (4 Berkas Wajib) ─────────────────────────

    public function uploadProof(Request $request)
    {
        $user = Auth::user();
        $resignation = ResignationRequest::where('user_id', $user->id)
            ->whereIn('status', [ResignationRequest::STATUS_PENDING_PROOF, ResignationRequest::STATUS_PROOF_REVISION])
            ->latest()
            ->first();

        if (!$resignation) {
            return back()->with('error', 'Pengajuan resign Anda saat ini tidak dalam tahap pengunggahan bukti.');
        }

        $request->validate([
            'pocket_proof' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:10240',
            'key_proof'    => 'required|file|image|mimes:jpeg,png,jpg,webp|max:10240',
            'letter_proof' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:10240',
            'fine_proof'   => 'required|file|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'pocket_proof.required' => 'Foto Kantong (screenshot full layar) wajib diunggah.',
            'key_proof.required'    => 'Foto Kunci (screenshot kunci tercabut) wajib diunggah.',
            'letter_proof.required' => 'Foto Surat Resign wajib diunggah.',
            'fine_proof.required'   => 'Foto Billing Denda Resign wajib diunggah.',
            'pocket_proof.image'    => 'Foto Kantong harus berupa berkas gambar yang valid.',
            'key_proof.image'       => 'Foto Kunci harus berupa berkas gambar yang valid.',
            'letter_proof.image'    => 'Foto Surat Resign harus berupa berkas gambar yang valid.',
            'fine_proof.image'      => 'Foto Billing Denda harus berupa berkas gambar yang valid.',
            '*.max'                 => 'Ukuran foto maksimal 10MB per berkas.',
        ]);

        // Simpan seluruh 4 berkas bukti ke storage publik
        $pocketPath = $request->file('pocket_proof')->store('resignation/proofs', 'public');
        $keyPath    = $request->file('key_proof')->store('resignation/proofs', 'public');
        $letterPath = $request->file('letter_proof')->store('resignation/proofs', 'public');
        $finePath   = $request->file('fine_proof')->store('resignation/proofs', 'public');

        $resignation->update([
            'pocket_proof'         => $pocketPath,
            'key_proof'            => $keyPath,
            'letter_proof'         => $letterPath,
            'fine_proof'           => $finePath,
            'proof_submitted_at'   => now(),
            'status'               => ResignationRequest::STATUS_PROOF_SUBMITTED,
        ]);

        return redirect()->route('portal.resignation.index')
            ->with('success', 'Seluruh berkas bukti resign berhasil dikirim! Menunggu cross-check dan verifikasi akhir dari Divisi IE.');
    }

    // ─── PND: Daftar & Approval Tahap 1 ──────────────────────────────────────

    public function managePnd(Request $request)
    {
        $this->checkIsPnd();

        $query = ResignationRequest::with(['user:id,name,staff_id,hospital', 'pndApprovedBy:id,name'])
            ->whereHas('user', fn($q) => $q->where('hospital', Auth::user()->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            // Default hanya tampilkan pengajuan yang butuh ditinjau PND (tidak termasuk cancelled)
            $query->where('status', '!=', ResignationRequest::STATUS_CANCELLED);
        }

        $requests = $query->paginate(30)->withQueryString();
        $stage    = 'pnd';

        return view('portal.resignation.manage', compact('requests', 'stage'));
    }

    public function pndApprove(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsPnd();
        $request->validate(['pnd_notes' => 'nullable|string|max:500']);

        if ($resignation->status !== ResignationRequest::STATUS_PENDING_PND) {
            return back()->with('error', 'Status pengajuan tidak sesuai.');
        }

        $resignation->update([
            'status'           => ResignationRequest::STATUS_PENDING_IE,
            'pnd_approved_by'  => Auth::id(),
            'pnd_approved_at'  => now(),
            'pnd_notes'        => $request->pnd_notes,
        ]);

        // Hitung denda otomatis
        $resignation->calculateFine();
        $resignation->save();

        // Kirim Notifikasi Discord Webhook dengan mention role I&E
        $this->sendDiscordIeResignationAlert($resignation);

        return back()->with('success', 'Resign disetujui PND. Berkas diteruskan ke IE untuk verifikasi denda.');
    }

    /**
     * Kirim notifikasi ke Discord Webhook untuk Divisi I&E
     */
    private function sendDiscordIeResignationAlert(ResignationRequest $resignation): void
    {
        try {
            $webhookUrl = env('DISCORD_WEBHOOK_IE', env('DISCORD_WEBHOOK_ABSENSI'));
            if (!$webhookUrl) return;

            $ieRoleId = env('DISCORD_ROLE_IE_ID', '');
            $mentionText = $ieRoleId ? "<@&{$ieRoleId}> " : "**[DIVISI I&E]** ";

            $embed = [
                'title'       => '📋 PEMBERITAHUAN RESIGN ANGGOTA — TAHAP I&E',
                'description' => "Pengajuan pengunduran diri staf telah **DISETUJUI OLEH PnD** dan siap diproses perhitungan dendanya oleh Divisi I&E.",
                'color'       => 0xF59E0B, // Amber/Orange
                'fields'      => [
                    [
                        'name'   => '👤 Nama Staf',
                        'value'  => $resignation->applicant_name ?? $resignation->user?->name ?? '-',
                        'inline' => true,
                    ],
                    [
                        'name'   => '🏷️ Jabatan / Role',
                        'value'  => $resignation->position ?? '-',
                        'inline' => true,
                    ],
                    [
                        'name'   => '💰 Total Gaji Pokok (Tanpa Bonus)',
                        'value'  => '$ ' . number_format($resignation->base_salary, 0, ',', '.'),
                        'inline' => true,
                    ],
                    [
                        'name'   => '📊 Persentase Denda',
                        'value'  => $resignation->fine_percentage . '%',
                        'inline' => true,
                    ],
                    [
                        'name'   => '💵 Total Denda Resign',
                        'value'  => '$ ' . number_format($resignation->fine_amount, 0, ',', '.'),
                        'inline' => true,
                    ],
                    [
                        'name'   => '✅ Disetujui PnD Oleh',
                        'value'  => Auth::user()->name,
                        'inline' => true,
                    ],
                ],
                'footer'      => [
                    'text' => 'Alta Hospital — Industrial & Employee Relations (IE)',
                ],
                'timestamp'   => now()->toISOString(),
            ];

            \Illuminate\Support\Facades\Http::timeout(5)->post($webhookUrl, [
                'content' => $mentionText . 'Terdapat pengajuan resign yang telah disetujui PnD dan membutuhkan konfirmasi perhitungan denda oleh I&E.',
                'embeds'  => [$embed],
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[Discord-IE-Resign] Gagal kirim webhook: ' . $e->getMessage());
        }
    }

    public function pndReject(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsPnd();
        $request->validate(['pnd_notes' => 'required|string|max:500']);

        $resignation->update([
            'status'          => ResignationRequest::STATUS_REJECTED,
            'pnd_approved_by' => Auth::id(),
            'pnd_approved_at' => now(),
            'pnd_notes'       => $request->pnd_notes,
        ]);

        return back()->with('success', 'Permohonan resign ' . $resignation->user->name . ' ditolak oleh PND.');
    }

    public function pndCancel(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsPnd();

        try {
            $applicantName = $resignation->applicant_name ?? $resignation->user?->name ?? 'anggota';

            // Pastikan akun staf tetap aktif
            if ($resignation->user) {
                $resignation->user->update(['is_active' => true]);
            }

            // Hapus pengajuan resign agar benar-benar hilang dari antrean
            $resignation->delete();

            return back()->with('success', 'Permohonan resign ' . $applicantName . ' berhasil dibatalkan dan dihapus dari antrean.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[Resignation] PND Cancel Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal membatalkan permohonan resign: ' . $e->getMessage());
        }
    }

    // ─── IE: Daftar & Verifikasi Tahap 2 & Tahap Bukti ────────────────────────

    public function manageIe(Request $request)
    {
        $this->checkIsIe();

        $query = ResignationRequest::with([
            'user:id,name,staff_id,citizen_id,hospital,role_id',
            'user.role',
            'pndApprovedBy:id,name',
            'ieVerifiedBy:id,name',
            'finalDeactivatedBy:id,name',
        ])
        ->whereHas('user', fn($q) => $q->where('hospital', Auth::user()->hospital ?? 'alta'))
        ->latest();

        // Otomatis update perhitungan denda untuk permohonan yang berstatus pending_ie
        // agar nominal denda selalu tersinkronisasi dengan riwayat penerimaan gaji pokok terbaru
        $pendingIeRequests = ResignationRequest::where('status', ResignationRequest::STATUS_PENDING_IE)->get();
        foreach ($pendingIeRequests as $pReq) {
            $pReq->calculateFine();
            $pReq->save();
        }

        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        } else {
            // Default tampilkan permohonan yang membutuhkan perhatian IE
            $query->whereIn('status', [
                ResignationRequest::STATUS_PENDING_IE,
                ResignationRequest::STATUS_PENDING_PROOF,
                ResignationRequest::STATUS_PROOF_SUBMITTED,
                ResignationRequest::STATUS_PROOF_REVISION,
            ]);
        }

        $requests = $query->paginate(30)->withQueryString();
        $stage    = 'ie';

        return view('portal.resignation.manage', compact('requests', 'stage'));
    }

    /**
     * IE: Konfirmasi Resign & Denda Awal.
     * PERUBAHAN: Akun anggota TIDAK langsung dinonaktifkan atau dihapus.
     * Data dipindahkan ke tahap Upload Bukti Resign oleh anggota.
     */
    public function ieVerifyPayment(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsIe();
        $request->validate(['ie_notes' => 'nullable|string|max:500']);

        if ($resignation->status !== ResignationRequest::STATUS_PENDING_IE) {
            return back()->with('error', 'Status tidak sesuai untuk konfirmasi denda IE.');
        }

        $resignation->update([
            'fine_paid'      => true,
            'ie_verified_by' => Auth::id(),
            'ie_verified_at' => now(),
            'ie_notes'       => $request->ie_notes,
            'status'         => ResignationRequest::STATUS_PENDING_PROOF,
        ]);

        // Catatan: Akun pengguna TETAP AKTIF (is_active = true) pada tahap ini agar dapat login dan mengunggah bukti
        return back()->with('success', 'Konfirmasi denda berhasil! Pengajuan ' . $resignation->applicant_name . ' dipindahkan ke tahap Upload Bukti Resign oleh anggota.');
    }

    /**
     * IE: Meminta Anggota Mengisi Ulang Formulir Bukti Resign jika bukti tidak sesuai.
     */
    public function ieRequestProofRevision(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsIe();
        $request->validate([
            'revision_notes' => 'required|string|max:1000',
        ], [
            'revision_notes.required' => 'Wajib menyertakan catatan revisi atau alasan ketidaksesuaian bukti.',
        ]);

        if (!in_array($resignation->status, [ResignationRequest::STATUS_PROOF_SUBMITTED, ResignationRequest::STATUS_PENDING_PROOF])) {
            return back()->with('error', 'Status tidak sesuai untuk meminta pengisian ulang bukti.');
        }

        $resignation->update([
            'status'               => ResignationRequest::STATUS_PROOF_REVISION,
            'proof_revision_notes' => $request->revision_notes,
        ]);

        return back()->with('info', 'Permintaan isi ulang form bukti resign telah dikirim ke ' . $resignation->applicant_name . '. Catatan perbaikan berhasil disimpan.');
    }

    /**
     * IE: Konfirmasi Akhir Penonaktifan.
     * Jika seluruh data dan bukti sudah sesuai:
     * 1. Status resign berubah menjadi completed.
     * 2. Status anggota (users) berubah menjadi Not Active (is_active = false).
     * 3. Sistem secara otomatis membuat dan menyimpan Log Resign sebagai riwayat administrasi permanen.
     */
    public function ieFinalConfirm(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsIe();
        $request->validate(['final_notes' => 'nullable|string|max:500']);

        if (!in_array($resignation->status, [ResignationRequest::STATUS_PROOF_SUBMITTED, ResignationRequest::STATUS_PENDING_PROOF])) {
            return back()->with('error', 'Pengajuan belum berada pada tahap siap konfirmasi akhir.');
        }

        $user = $resignation->user;

        // 1. Perbarui data pengajuan resign
        $resignation->update([
            'status'               => ResignationRequest::STATUS_COMPLETED,
            'final_deactivated_by' => Auth::id(),
            'final_deactivated_at' => now(),
        ]);

        // 2. Ubah status anggota menjadi Not Active
        if ($user) {
            $user->update(['is_active' => false]);
        }

        // 3. Simpan Log Resign secara otomatis ke riwayat administrasi permanen
        ResignationLog::create([
            'resignation_request_id' => $resignation->id,
            'user_id'                => $user?->id,
            'member_name'            => $resignation->applicant_name ?? $user?->name ?? 'Anggota',
            'citizen_id'             => $user?->citizen_id ?? '-',
            'last_position'          => $user?->role?->display_name ?? $resignation->position ?? '-',
            'managerial_position'    => $resignation->managerial_position ?? '-',
            'batch'                  => $resignation->batch ?? '-',
            'hospital'               => $user?->hospital ?? 'alta',
            'resignation_date'       => $resignation->letter_date ?? now()->toDateString(),
            'deactivated_at'         => now(),
            'reason'                 => "Alasan IC:\n" . ($resignation->reason_ic ?? '-') . "\n\nAlasan OOC:\n" . ($resignation->reason_ooc ?? '-'),
            'reason_ic'              => $resignation->reason_ic,
            'reason_ooc'             => $resignation->reason_ooc,
            'total_fine'             => $resignation->fine_amount ?? 0,
            'fine_percentage'        => $resignation->fine_percentage ?? 0,
            'fine_status'            => $resignation->fine_paid ? 'Lunas' : 'Belum Lunas',
            'pocket_proof'           => $resignation->pocket_proof,
            'key_proof'              => $resignation->key_proof,
            'letter_proof'           => $resignation->letter_proof,
            'fine_proof'             => $resignation->fine_proof,
            'ie_verifier_name'       => $resignation->ieVerifiedBy?->name ?? Auth::user()->name,
            'ie_deactivator_name'    => Auth::user()->name,
            'notes'                  => $request->final_notes,
        ]);

        return back()->with('success', 'Konfirmasi akhir berhasil! Status anggota ' . ($resignation->applicant_name ?? $user?->name) . ' telah diubah menjadi Not Active dan Log Resign resmi telah tersimpan di arsip.');
    }

    public function ieCancel(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsIe();

        try {
            $applicantName = $resignation->applicant_name ?? $resignation->user?->name ?? 'anggota';

            // Pastikan akun staf tetap aktif
            if ($resignation->user) {
                $resignation->user->update(['is_active' => true]);
            }

            // Hapus pengajuan resign agar benar-benar hilang dari antrean
            $resignation->delete();

            return back()->with('success', 'Permohonan resign ' . $applicantName . ' berhasil dibatalkan dan dihapus dari antrean IE.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[Resignation] IE Cancel Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Gagal membatalkan permohonan resign: ' . $e->getMessage());
        }
    }

    // ─── LOG RESIGN: Arsip & Audit Riwayat Resign ─────────────────────────────

    public function logs(Request $request)
    {
        $this->checkCanViewLogs();

        $query = ResignationLog::query()->latest('deactivated_at');

        // Filter kata kunci (Nama, Citizen ID, Jabatan)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('member_name', 'LIKE', "%{$search}%")
                  ->orWhere('citizen_id', 'LIKE', "%{$search}%")
                  ->orWhere('last_position', 'LIKE', "%{$search}%")
                  ->orWhere('ie_verifier_name', 'LIKE', "%{$search}%")
                  ->orWhere('ie_deactivator_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter rentang tanggal nonaktif
        if ($from = $request->get('date_from')) {
            $query->whereDate('deactivated_at', '>=', $from);
        }
        if ($to = $request->get('date_to')) {
            $query->whereDate('deactivated_at', '<=', $to);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('portal.resignation.logs', compact('logs'));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeView(ResignationRequest $resignation): void
    {
        $user = Auth::user();
        if ($resignation->user_id !== $user->id
            && !$user->isManagerOrAbove()
            && !$user->isInDivision('pnd', 'ie')) {
            abort(403);
        }
    }

    private function checkIsPnd(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('pnd')) {
            abort(403, 'Hanya divisi PND yang dapat memverifikasi resign di tahap ini.');
        }
    }

    private function checkIsIe(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('ie')) {
            abort(403, 'Hanya divisi IE yang dapat memverifikasi pelunasan denda resign.');
        }
    }

    private function checkCanViewLogs(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isManagerOrAbove() && !$user->isInDivision('ie', 'pnd')) {
            abort(403, 'Anda tidak memiliki wewenang untuk mengakses arsip Log Resign.');
        }
    }
}
