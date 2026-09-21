<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PromotionPeriod;
use App\Models\PromotionApplication;
use App\Models\StaseApplication;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionController extends Controller
{
    private function checkIsPnd(): void
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isExecutiveOrAbove() && !$user->isInDivision('pnd')) {
            abort(403, 'Hanya divisi PND yang dapat mengelola kenaikan jabatan.');
        }
    }

    // ─── Anggota: Form Pengajuan Kenaikan Jabatan ─────────────────────────────

    public function index()
    {
        $user   = Auth::user();
        $period = PromotionPeriod::currentOpen($user->hospital ?? 'alta');

        // Pengajuan terakhir user
        $latestApp = PromotionApplication::with(['targetRole', 'period'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('portal.promotion.index', compact('user', 'period', 'latestApp'));
    }

    public function create(Request $request)
    {
        $user   = Auth::user();
        $period = PromotionPeriod::currentOpen($user->hospital ?? 'alta');

        if (!$period) {
            return redirect()->route('portal.promotion.index')
                ->with('error', 'Periode kenaikan jabatan sedang ditutup. Silakan tunggu pengumuman dari PND.');
        }

        // Cek apakah ada pengajuan pending di periode ini
        $existingApp = PromotionApplication::where('user_id', $user->id)
            ->where('period_id', $period->id)
            ->where('status', 'pending')
            ->first();

        if ($existingApp) {
            return redirect()->route('portal.promotion.index')
                ->with('info', 'Pengajuan kenaikan jabatan Anda sedang dalam proses tinjauan PND.');
        }

        // Ambil semua jabatan sebagai target promosi (lebih tinggi dari jabatan sekarang)
        $currentLevel = $user->role?->level ?? 0;
        $targetRoles  = StaffRole::where('level', '>', $currentLevel)
            ->where('level', '<', 5) // Maks Supervisor / Dokter Spesialis
            ->orderBy('level')
            ->get();

        // Default build checklist untuk jabatan terdekat
        $defaultTarget = $targetRoles->first();
        $checklist = $defaultTarget ? $user->buildPromotionChecklist($defaultTarget) : [];

        return view('portal.promotion.create', compact('user', 'period', 'targetRoles', 'checklist', 'defaultTarget'));
    }

    /**
     * AJAX: Ambil checklist persyaratan berdasarkan target_role_id yang dipilih user.
     * GET /portal/promotion/checklist?target_role_id=X
     */
    public function checklistApi(Request $request)
    {
        $user = Auth::user();
        $role = StaffRole::find($request->target_role_id);
        if (!$role) return response()->json([]);

        $checklist = $user->buildPromotionChecklist($role);
        $allMet    = collect($checklist)->every(fn($r) => $r['met'] === true);

        return response()->json([
            'checklist' => $checklist,
            'all_met'   => $allMet,
        ]);
    }

    public function store(Request $request)
    {
        $user   = Auth::user();
        $period = PromotionPeriod::currentOpen($user->hospital ?? 'alta');

        if (!$period) {
            return back()->with('error', 'Periode kenaikan jabatan sedang ditutup.');
        }

        $request->validate([
            'target_role_id'          => 'required|exists:staff_roles,id',
            'case_study_file'         => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'recommendation_letter_1' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'recommendation_letter_2' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'supporting_document'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $targetRole = StaffRole::findOrFail($request->target_role_id);
        $checklist  = $user->buildPromotionChecklist($targetRole);

        // Upload files
        $caseStudy = $request->hasFile('case_study_file')
            ? $request->file('case_study_file')->store('promotions/case-study', 'public')
            : null;

        $rec1 = $request->hasFile('recommendation_letter_1')
            ? $request->file('recommendation_letter_1')->store('promotions/recommendations', 'public')
            : null;

        $rec2 = $request->hasFile('recommendation_letter_2')
            ? $request->file('recommendation_letter_2')->store('promotions/recommendations', 'public')
            : null;

        $supportingDoc = $request->hasFile('supporting_document')
            ? $request->file('supporting_document')->store('promotions/supporting', 'public')
            : null;

        // Update checklist untuk item yang membutuhkan file upload
        $checklist = array_map(function ($item) use ($caseStudy, $rec1, $rec2) {
            if ($item['key'] === 'case_study_file') {
                $item['met'] = !empty($caseStudy);
            }
            if ($item['key'] === 'recommendation_letter') {
                $item['met'] = !empty($rec1) && !empty($rec2);
            }
            return $item;
        }, $checklist);

        PromotionApplication::create([
            'user_id'                    => $user->id,
            'period_id'                  => $period->id,
            'current_role_id'            => $user->role_id,
            'target_role_id'             => $targetRole->id,
            'credit_score_at_submission' => $user->getCreditBalance(),
            'training_days'              => $user->getDaysActiveSinceJoining(),
            'duty_hours'                 => $user->getTotalDutyHours(),
            'requirements_checklist'     => $checklist,
            'case_study_file'            => $caseStudy,
            'recommendation_letter_1'    => $rec1,
            'recommendation_letter_2'    => $rec2,
            'supporting_document'        => $supportingDoc,
            'status'                     => PromotionApplication::STATUS_PENDING,
        ]);

        return redirect()->route('portal.promotion.index')
            ->with('success', 'Pengajuan kenaikan jabatan berhasil dikirim ke PND untuk ditinjau.');
    }

    // ─── PND: Kontrol Periode ─────────────────────────────────────────────────

    public function periodIndex()
    {
        $this->checkIsPnd();
        $user = Auth::user();

        $periods = PromotionPeriod::with(['openedBy:id,name', 'closedBy:id,name'])
            ->where('hospital', $user->hospital ?? 'alta')
            ->latest()
            ->paginate(20);

        $currentPeriod = PromotionPeriod::currentOpen($user->hospital ?? 'alta');

        return view('portal.promotion.periods', compact('periods', 'currentPeriod'));
    }

    public function openPeriod(Request $request)
    {
        $this->checkIsPnd();
        $request->validate([
            'name'  => 'required|string|max:255',
            'batch' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $hospital = $user->hospital ?? 'alta';

        // Tutup periode lama jika ada
        PromotionPeriod::where('hospital', $hospital)->where('is_open', true)->update([
            'is_open'   => false,
            'closed_by' => Auth::id(),
        ]);

        PromotionPeriod::create([
            'name'       => $request->name,
            'batch'      => $request->batch,
            'hospital'   => $hospital,
            'start_date' => now()->toDateString(),
            'is_open'    => true,
            'opened_by'  => Auth::id(),
            'notes'      => $request->notes,
        ]);

        return back()->with('success', 'Periode kenaikan jabatan "' . $request->name . '" berhasil dibuka.');
    }

    public function closePeriod(PromotionPeriod $period)
    {
        $this->checkIsPnd();
        $period->update([
            'is_open'    => false,
            'end_date'   => now()->toDateString(),
            'closed_by'  => Auth::id(),
        ]);
        return back()->with('success', 'Periode kenaikan jabatan "' . $period->name . '" berhasil ditutup.');
    }

    // ─── PND: Review Pengajuan ────────────────────────────────────────────────

    public function manageApplications(Request $request)
    {
        $this->checkIsPnd();
        $user = Auth::user();

        $query = PromotionApplication::with([
                'user:id,name,staff_id,hospital',
                'currentRole:id,name,display_name',
                'targetRole:id,name,display_name',
                'period:id,name,batch',
            ])
            ->whereHas('user', fn($q) => $q->where('hospital', $user->hospital ?? 'alta'))
            ->latest();

        if ($status = $request->get('status')) $query->where('status', $status);
        if ($periodId = $request->get('period_id')) $query->where('period_id', $periodId);

        $applications = $query->paginate(30)->withQueryString();
        $periods = PromotionPeriod::where('hospital', $user->hospital ?? 'alta')->latest()->get(['id', 'name', 'batch']);

        return view('portal.promotion.applications', compact('applications', 'periods'));
    }

    public function reviewApplication(Request $request, PromotionApplication $application)
    {
        $this->checkIsPnd();
        $request->validate([
            'action'    => 'required|in:approve,reject',
            'pnd_notes' => 'nullable|string|max:500',
        ]);

        $isApproved = $request->action === 'approve';

        $application->update([
            'status'          => $isApproved ? 'approved' : 'rejected',
            'approved_by_pnd' => Auth::id(),
            'pnd_reviewed_at' => now(),
            'pnd_notes'       => $request->pnd_notes,
        ]);

        // Jika disetujui, naikkan jabatan user
        if ($isApproved) {
            $application->user->update(['role_id' => $application->target_role_id]);
        }

        $msg = $isApproved
            ? 'Kenaikan jabatan ' . $application->user->name . ' disetujui. Jabatan otomatis diperbarui.'
            : 'Pengajuan kenaikan jabatan ' . $application->user->name . ' ditolak.';

        return back()->with('success', $msg);
    }
}
