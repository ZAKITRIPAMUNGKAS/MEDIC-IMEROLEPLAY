<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ResignationRequest;
use App\Models\OrganizationalStructure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->whereNotIn('status', ['completed', 'rejected'])
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
            ->whereNotIn('status', ['completed', 'rejected'])
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
        $baseSalary = PayrollHelper::getBaseSalary($user->role?->name);

        // Generate standard text
        $today = Carbon::today()->isoFormat('D MMMM Y');
        $standardText = "Dengan hormat,\n\nSaya yang bertanda tangan di bawah ini:\n\nNama : {$user->name}\nJabatan : {$position}\n\nMenyatakan mengundurkan diri dari posisi yang saya emban di Alta Hospital terhitung sejak surat ini dibuat.\n\nSaya mengucapkan terima kasih atas kesempatan dan kepercayaan yang telah diberikan selama ini. Mohon maaf atas segala kesalahan yang pernah terjadi selama saya bertugas.\n\nHormat saya,\n{$user->name}";

        $resign = ResignationRequest::create([
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

        return back()->with('success', 'Resign disetujui PND. Berkas diteruskan ke IE untuk kalkulasi denda.');
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

    // ─── IE: Daftar & Verifikasi Tahap 2 (Denda) ─────────────────────────────

    public function manageIe(Request $request)
    {
        $this->checkIsIe();

        $query = ResignationRequest::with(['user:id,name,staff_id,hospital', 'pndApprovedBy:id,name', 'ieVerifiedBy:id,name'])
            ->whereHas('user', fn($q) => $q->where('hospital', Auth::user()->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            // Default hanya tampilkan pengajuan yang butuh diverifikasi IE
            $query->where('status', ResignationRequest::STATUS_PENDING_IE);
        }

        $requests = $query->paginate(30)->withQueryString();
        $stage    = 'ie';

        return view('portal.resignation.manage', compact('requests', 'stage'));
    }

    public function ieVerifyPayment(Request $request, ResignationRequest $resignation)
    {
        $this->checkIsIe();
        $request->validate(['ie_notes' => 'nullable|string|max:500']);

        if ($resignation->status !== ResignationRequest::STATUS_PENDING_IE) {
            return back()->with('error', 'Status tidak sesuai untuk verifikasi IE.');
        }

        $resignation->update([
            'fine_paid'      => true,
            'ie_verified_by' => Auth::id(),
            'ie_verified_at' => now(),
            'ie_notes'       => $request->ie_notes,
            'status'         => ResignationRequest::STATUS_COMPLETED,
        ]);

        // Nonaktifkan akun pengguna setelah pelunasan denda
        $resignation->user->update(['is_active' => false]);

        return back()->with('success', 'Denda lunas. Akun ' . $resignation->user->name . ' telah dinonaktifkan.');
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
}
