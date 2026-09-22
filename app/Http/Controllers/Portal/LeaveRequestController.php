<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    // ─── Anggota: Form & Daftar Pengajuan Cuti ───────────────────────────────

    public function index()
    {
        $user     = Auth::user();
        $today    = Carbon::today()->toDateString();
        $requests = LeaveRequest::with('approvedBy:id,name,staff_id')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        // Cari apakah ada cuti yang sedang aktif (disetujui dan tanggal selesai >= hari ini)
        $activeLeave = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('end_date', '>=', $today)
            ->orderBy('end_date', 'desc')
            ->first();

        return view('portal.leave.index', compact('requests', 'activeLeave'));
    }

    public function create()
    {
        $user  = Auth::user();
        $today = Carbon::today()->toDateString();

        // Cek apakah ada cuti yang masih aktif
        $activeLeave = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('end_date', '>=', $today)
            ->orderBy('end_date', 'desc')
            ->first();

        if ($activeLeave) {
            $startFormatted = Carbon::parse($activeLeave->start_date)->locale('id')->translatedFormat('d M Y');
            $endFormatted   = Carbon::parse($activeLeave->end_date)->locale('id')->translatedFormat('d M Y');
            return redirect()->route('portal.leave.index')
                ->with('error', "Masa cuti Anda masih aktif ({$startFormatted} s/d {$endFormatted}). Anda tidak dapat mengajukan permohonan cuti baru sampai masa cuti berakhir.");
        }

        return view('portal.leave.create', [
            'user'        => $user,
            'letterDate'  => Carbon::today()->format('d/m/Y'),
            'position'    => $user->role?->display_name ?? '-',
        ]);
    }

    public function store(Request $request)
    {
        $user  = Auth::user();
        $today = Carbon::today()->toDateString();

        // Validasi: Cek apakah masa cuti masih aktif
        $activeLeave = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('end_date', '>=', $today)
            ->orderBy('end_date', 'desc')
            ->first();

        if ($activeLeave) {
            $startFormatted = Carbon::parse($activeLeave->start_date)->locale('id')->translatedFormat('d M Y');
            $endFormatted   = Carbon::parse($activeLeave->end_date)->locale('id')->translatedFormat('d M Y');
            return redirect()->route('portal.leave.index')
                ->with('error', "Pengajuan cuti ditolak: Anda masih memiliki masa cuti aktif ({$startFormatted} s/d {$endFormatted}). Anda tidak dapat mengajukan cuti baru sampai masa cuti selesai.");
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:' . $today,
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason_ic'  => 'required|string|max:1000',
            'reason_ooc' => 'required|string|max:1000',
        ], [
            'start_date.after_or_equal' => 'Pengajuan cuti tidak dapat dimulai sebelum tanggal hari ini / tanggal pengajuan dibuat.',
            'end_date.after_or_equal'   => 'Tanggal selesai cuti harus sama atau setelah tanggal mulai cuti.',
        ]);

        $start    = Carbon::parse($validated['start_date'])->startOfDay();
        $end      = Carbon::parse($validated['end_date'])->startOfDay();
        $duration = $start->diffInDays($end) + 1;

        if ($duration > 30) {
            return back()->withInput()->withErrors([
                'end_date' => 'Pengajuan cuti tidak dapat dikirim karena melebihi batas maksimal 30 hari. (Durasi pengajuan Anda: ' . $duration . ' hari).',
            ]);
        }

        LeaveRequest::create([
            'user_id'        => $user->id,
            'letter_date'    => now()->toDateString(),
            'subject'        => 'Izin Cuti',
            'recipient'      => 'Yth. Direktur IME Medical Center di Tempat',
            'applicant_name' => $user->name,
            'position'       => $user->role?->display_name ?? '-',
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date'],
            'duration_days'  => $duration,
            'reason_ic'      => $validated['reason_ic'],
            'reason_ooc'     => $validated['reason_ooc'],
            'status'         => 'approved', // Otomatis disetujui tanpa perlu menunggu ACC manual
            'approved_at'    => now(),
        ]);

        return redirect()->route('portal.leave.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim dan otomatis disetujui.');
    }

    /**
     * Daftar cuti seluruh staf medis yang sedang/akan cuti (dapat dilihat seluruh anggota medis).
     */
    public function publicList(Request $request)
    {
        $user = Auth::user();
        $sortBy  = $request->get('sort_by', 'start_date');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        // Hanya kolom yang diizinkan untuk di-sort
        if (!in_array($sortBy, ['start_date', 'end_date'])) {
            $sortBy = 'start_date';
        }

        $query = LeaveRequest::with(['user:id,name,staff_id,hospital', 'approvedBy:id,name'])
            ->where('status', 'approved')
            ->where('end_date', '>=', Carbon::today()->subDays(7)) // Tampilkan yang baru saja/sedang/akan cuti
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->orderBy($sortBy, $sortDir);

        $leaves = $query->paginate(25)->withQueryString();

        return view('portal.leave.public-list', compact('leaves', 'sortBy', 'sortDir'));
    }

    public function show(LeaveRequest $leave)
    {
        $this->authorizeView($leave);
        return view('portal.leave.show', compact('leave'));
    }

    // ─── Admin / Manager: Approve atau Tolak ─────────────────────────────────

    public function approve(LeaveRequest $leave)
    {
        $this->checkCanApprove();

        $leave->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Permohonan cuti ' . $leave->user->name . ' disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leave)
    {
        $this->checkCanApprove();

        $request->validate(['notes' => 'nullable|string|max:500']);

        $leave->update([
            'status'      => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'Permohonan cuti ' . $leave->user->name . ' ditolak.');
    }

    // ─── Daftar Semua Pengajuan (Manager ke atas) ─────────────────────────────

    public function manage(Request $request)
    {
        $this->checkCanApprove();

        $user  = Auth::user();
        $query = LeaveRequest::with(['user:id,name,staff_id', 'approvedBy:id,name,staff_id'])
            ->where(function ($q) use ($user) {
                if (!$user->isAdmin()) {
                    $q->whereHas('user', fn($u) => $u->where('hospital', $user->hospital ?? 'alta'));
                }
            })
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(30)->withQueryString();

        return view('portal.leave.manage', compact('requests'));
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeView(LeaveRequest $leave): void
    {
        $user = Auth::user();
        if ($leave->user_id !== $user->id && !$user->isManagerOrAbove() && !$user->isInDivision('ie') && !$user->isAdmin()) {
            abort(403);
        }
    }

    private function checkCanApprove(): void
    {
        $user = Auth::user();
        if (!$user->isManagerOrAbove() && !$user->isInDivision('ie') && !$user->isAdmin()) {
            abort(403, 'Hanya Divisi IE dan Manajemen yang dapat mengelola dan menyetujui cuti.');
        }
    }
}
