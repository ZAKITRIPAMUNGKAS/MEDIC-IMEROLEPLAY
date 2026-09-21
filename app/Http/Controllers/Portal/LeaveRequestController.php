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
        $requests = LeaveRequest::with('approvedBy:id,name,staff_id')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(20);

        return view('portal.leave.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user();
        return view('portal.leave.create', [
            'user'        => $user,
            'letterDate'  => Carbon::today()->format('d/m/Y'),
            'position'    => $user->role?->display_name ?? '-',
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason_ic'  => 'required|string|max:1000',
            'reason_ooc' => 'required|string|max:1000',
        ]);

        $start   = Carbon::parse($validated['start_date']);
        $end     = Carbon::parse($validated['end_date']);
        $duration = $start->diffInDays($end) + 1;

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
            'status'         => 'pending',
        ]);

        return redirect()->route('portal.leave.index')
            ->with('success', 'Pengajuan cuti berhasil dikirim.');
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
        if ($leave->user_id !== $user->id && !$user->isManagerOrAbove()) {
            abort(403);
        }
    }

    private function checkCanApprove(): void
    {
        if (!Auth::user()->isManagerOrAbove()) {
            abort(403, 'Hanya Manager ke atas yang dapat menyetujui cuti.');
        }
    }
}
