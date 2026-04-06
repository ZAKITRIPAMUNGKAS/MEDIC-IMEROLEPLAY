<?php

namespace App\Http\Controllers;

use App\Models\MeetingRequest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MeetingRequestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware handled in web.php
    }

    // ===========================
    // Staff-facing methods
    // ===========================

    /**
     * Display staff's own meeting requests (history)
     */
    public function index()
    {
        $user = Auth::user();

        $requests = MeetingRequest::where('user_id', $user->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = MeetingRequest::where('user_id', $user->id)->pending()->count();
        $approvedCount = MeetingRequest::where('user_id', $user->id)->approved()->count();
        $rejectedCount = MeetingRequest::where('user_id', $user->id)->rejected()->count();

        return view('staff.meeting-requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $user = Auth::user();
        $recentRequests = MeetingRequest::where('user_id', $user->id)
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('staff.meeting-requests.create', compact('recentRequests'));
    }

    /**
     * Store a new meeting request
     */
    public function store(Request $request)
    {
        $request->validate([
            'requested_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'required|string|min:10|max:1000',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ], [
            'requested_date.required' => 'Tanggal meeting harus diisi.',
            'start_time.required' => 'Waktu mulai harus diisi.',
            'end_time.required' => 'Waktu selesai harus diisi.',
            'reason.required' => 'Alasan meeting harus diisi.',
            'reason.min' => 'Alasan meeting minimal 10 karakter.',
            'reason.max' => 'Alasan meeting maksimal 1000 karakter.',
            'photo.required' => 'Bukti foto meeting harus diunggah.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'photo.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        $user = Auth::user();

        // Check for duplicate pending requests on the same date
        $existingPending = MeetingRequest::where('user_id', $user->id)
            ->where('requested_date', $request->requested_date)
            ->pending()
            ->exists();

        if ($existingPending) {
            return back()->with('error', 'Anda sudah memiliki pengajuan meeting yang masih menunggu persetujuan pada tanggal tersebut.')->withInput();
        }

        // Validate duration (max 5 hours)
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        if ($end->lt($start)) {
            $end->addDay();
        }
        $durationMinutes = $start->diffInMinutes($end);

        if ($durationMinutes > 300) {
            return back()->with('error', 'Durasi meeting tidak boleh lebih dari 5 jam (300 menit).')->withInput();
        }

        if ($durationMinutes < 15) {
            return back()->with('error', 'Durasi meeting minimal 15 menit.')->withInput();
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $publicPath = public_path('uploads/meeting-proofs');
            $destinationPath = $publicPath . '/' . $fileName;

            // Create directory if it doesn't exist
            if (!is_dir($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            // Compress and save image
            $compressed = \App\Helpers\ImageHelper::compressUploadedImage(
                $file,
                $destinationPath,
                1000, // max width for proof
                1000, // max height
                80    // quality
            );

            if (!$compressed) {
                $file->move($publicPath, $fileName);
            }

            $photoPath = 'uploads/meeting-proofs/' . $fileName;
        }

        MeetingRequest::create([
            'user_id' => $user->id,
            'requested_date' => $request->requested_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        return redirect()->route('staff.meeting-requests.index')
            ->with('success', 'Pengajuan meeting berhasil dikirim. Menunggu persetujuan admin.');
    }

    // ===========================
    // Admin-facing methods
    // ===========================

    /**
     * Display all meeting requests for admin review
     */
    public function adminIndex(Request $request)
    {
        $query = MeetingRequest::with(['user', 'reviewer']);

        // Filter by status
        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search by name
        if ($request->filled('q')) {
            $q = trim($request->get('q'));
            $query->whereHas('user', function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        $pendingCount = MeetingRequest::pending()->count();
        $approvedCount = MeetingRequest::approved()->count();
        $rejectedCount = MeetingRequest::rejected()->count();

        return view('admin.meeting-requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'status'
        ));
    }

    /**
     * Approve a meeting request and inject attendance record
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $meetingRequest = MeetingRequest::findOrFail($id);

        if (!$meetingRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diproses sebelumnya.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $workDate = $meetingRequest->requested_date;
            $clockIn = Carbon::parse(Carbon::parse($workDate)->format('Y-m-d') . ' ' . $meetingRequest->start_time, 'Asia/Jakarta');
            $clockOut = Carbon::parse(Carbon::parse($workDate)->format('Y-m-d') . ' ' . $meetingRequest->end_time, 'Asia/Jakarta');

            // Handle cross-day
            if ($clockOut->lt($clockIn)) {
                $clockOut->addDay();
            }

            $durationSeconds = $clockIn->diffInSeconds($clockOut);
            $durationMinutes = floor($durationSeconds / 60);

            // Get next session number
            $sessionNumber = Attendance::getNextSessionNumber($meetingRequest->user_id, $workDate);

            // Create attendance record
            $attendance = Attendance::create([
                'user_id' => $meetingRequest->user_id,
                'work_date' => $workDate,
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'session_duration' => $durationSeconds,
                'total_hours' => max(1, $durationMinutes),
                'session_number' => $sessionNumber,
                'session_type' => 'meeting',
                'is_active' => false,
                'notes' => sprintf(
                    "[Meeting Request #%d — Approved by %s]\nAlasan: %s",
                    $meetingRequest->id,
                    auth()->user()->name,
                    $meetingRequest->reason
                ),
            ]);

            // Update meeting request
            $meetingRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
                'injected_attendance_id' => $attendance->id,
            ]);

            DB::commit();

            \Log::info('Meeting request approved', [
                'meeting_request_id' => $meetingRequest->id,
                'attendance_id' => $attendance->id,
                'user_id' => $meetingRequest->user_id,
                'approved_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan meeting disetujui dan jam kerja berhasil ditambahkan.',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'duration' => $attendance->getFormattedDuration(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Failed to approve meeting request', [
                'meeting_request_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a meeting request
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'review_notes' => 'required|string|min:5|max:500',
        ], [
            'review_notes.required' => 'Alasan penolakan harus diisi.',
            'review_notes.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $meetingRequest = MeetingRequest::findOrFail($id);

        if (!$meetingRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini sudah diproses sebelumnya.'
            ], 400);
        }

        $meetingRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes,
        ]);

        \Log::info('Meeting request rejected', [
            'meeting_request_id' => $meetingRequest->id,
            'user_id' => $meetingRequest->user_id,
            'rejected_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan meeting ditolak.'
        ]);
    }

    public function undoProcess($id)
    {
        $meetingRequest = MeetingRequest::findOrFail($id);

        if ($meetingRequest->isPending()) {
            return back()->with('error', 'Pengajuan masih dalam status pending.');
        }

        if (!$meetingRequest->reviewed_at || $meetingRequest->reviewed_at->diffInMinutes(now()) > 60) {
            return back()->with('error', 'Batas waktu pembatalan (1 jam) telah berakhir.');
        }

        DB::beginTransaction();

        try {
            // Jika approved, hapus attendance yang disuntikkan
            if ($meetingRequest->status === 'approved' && $meetingRequest->injected_attendance_id) {
                Attendance::where('id', $meetingRequest->injected_attendance_id)->delete();
            }

            $meetingRequest->update([
                'status' => 'pending',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'review_notes' => null,
                'injected_attendance_id' => null,
            ]);

            DB::commit();

            $message = 'Aksi berhasil dibatalkan. Status pengajuan kembali ke Pending.';
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membatalkan aksi: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal membatalkan aksi: ' . $e->getMessage());
        }
    }
}
