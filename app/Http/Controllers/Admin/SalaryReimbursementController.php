<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryReimbursement;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalaryReimbursementController extends Controller
{
    /**
     * Display a listing of salary reimbursements.
     */
    public function index(Request $request)
    {
        // Log request for debugging
        \Illuminate\Support\Facades\Log::info('Reimbursement index filter', ['params' => $request->all()]);

        // Check if user has permission
        if (!auth()->user()->hasPermission('manage_reimbursements')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Get filter parameters
        $week = $request->input('week');
        $managerId = $request->input('manager_id');
        $status = $request->input('status');

        // Convert week to date range if provided
        $periodStart = null;
        $periodEnd = null;
        if ($week && !empty($week)) {
            try {
                // Week format: YYYY-Www (e.g., 2026-W04)
                list($year, $weekNum) = explode('-W', $week);
                $dto = new \DateTime();
                $dto->setISODate($year, $weekNum);
                $periodStart = $dto->format('Y-m-d'); // Monday
                $dto->modify('+6 days');
                $periodEnd = $dto->format('Y-m-d'); // Sunday
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Reimbursement week filter error', ['error' => $e->getMessage()]);
            }
        }

        // 1. Build Base Query for common filters (Week & Manager)
        $baseQuery = SalaryReimbursement::query();

        if ($periodStart && $periodEnd) {
            $baseQuery->whereBetween('period_start', [$periodStart, $periodEnd]);
        }

        if ($managerId && !empty($managerId)) {
            $baseQuery->where('manager_id', $managerId);
        }

        // 2. Calculate summary using the filtered base query (BEFORE applying status filter)
        $summary = [
            'total_pending' => (clone $baseQuery)->pending()->sum('total_amount'),
            'total_reimbursed' => (clone $baseQuery)->reimbursed()->sum('total_amount'),
            'pending_count' => (clone $baseQuery)->pending()->count(),
            'reimbursed_count' => (clone $baseQuery)->reimbursed()->count(),
        ];

        // 3. Build Final Query for the table (including Manager & status)
        $query = $baseQuery->with(['manager', 'reimbursedBy'])
            ->orderBy('period_start', 'desc')
            ->orderBy('is_reimbursed', 'asc'); // Show pending first

        if ($status === 'pending') {
            $query->pending();
        } elseif ($status === 'reimbursed') {
            $query->reimbursed();
        }

        // Paginate and preserve query string
        $reimbursements = $query->paginate(20)->withQueryString();

        // Get list of managers who have paid salaries (for the filter dropdown)
        $managers = User::whereHas('paidPayrolls')
            ->orderBy('name')
            ->get();

        return view('admin.reimbursements.index', compact('reimbursements', 'managers', 'summary'));
    }

    /**
     * Show the form for creating a new reimbursement record or calculate for a period.
     */
    public function calculatePeriod(Request $request)
    {
        // Check if user has permission
        if (!auth()->user()->hasPermission('manage_reimbursements')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        try {
            DB::beginTransaction();

            // Get all paid payrolls in this period grouped by who paid them
            $payrollsByManager = Payroll::where('status', 'paid')
                ->whereBetween('paid_at', [$periodStart->startOfDay(), $periodEnd->endOfDay()])
                ->whereNotNull('paid_by')
                ->select('paid_by', DB::raw('SUM(calculated_salary) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('paid_by')
                ->get();

            if ($payrollsByManager->isEmpty()) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada gaji yang dibayarkan dalam periode ini.',
                ], 400);
            }

            $created = 0;
            $updated = 0;

            foreach ($payrollsByManager as $group) {
                // Check if reimbursement record already exists
                $reimbursement = SalaryReimbursement::where('manager_id', $group->paid_by)
                    ->where('period_start', $periodStart->toDateString())
                    ->where('period_end', $periodEnd->toDateString())
                    ->first();

                $data = [
                    'total_amount' => $group->total,
                    'payroll_count' => $group->count,
                ];

                if ($reimbursement) {
                    // Update existing record (but don't change reimbursement status)
                    $reimbursement->update($data);
                    $updated++;
                } else {
                    // Create new record
                    SalaryReimbursement::create([
                        'manager_id' => $group->paid_by,
                        'period_start' => $periodStart->toDateString(),
                        'period_end' => $periodEnd->toDateString(),
                        ...$data,
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil! {$created} record baru dibuat, {$updated} record diperbarui.",
                'data' => [
                    'created' => $created,
                    'updated' => $updated,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified salary reimbursement with payroll breakdown.
     */
    public function show(SalaryReimbursement $reimbursement)
    {
        // Check if user has permission
        if (!auth()->user()->hasPermission('manage_reimbursements')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // Load relationships
        $reimbursement->load(['manager', 'reimbursedBy']);

        // Get all payrolls paid by this manager in this period
        $payrolls = Payroll::where('paid_by', $reimbursement->manager_id)
            ->where('status', 'paid')
            ->whereBetween('paid_at', [
                $reimbursement->period_start->startOfDay(),
                $reimbursement->period_end->endOfDay()
            ])
            ->with('user')
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('admin.reimbursements.show', compact('reimbursement', 'payrolls'));
    }

    /**
     * Mark a reimbursement as reimbursed.
     */
    public function markAsReimbursed(Request $request, SalaryReimbursement $reimbursement)
    {
        // Check if user has permission
        if (!auth()->user()->hasPermission('manage_reimbursements')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.'
            ], 403);
        }

        // Check if already reimbursed
        if ($reimbursement->isReimbursed()) {
            return response()->json([
                'success' => false,
                'message' => 'Reimbursement ini sudah ditandai sebagai direimburse.',
            ], 400);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $reimbursement->markAsReimbursed(auth()->id(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Reimbursement berhasil ditandai sebagai direimburse.',
                'redirect_url' => route('admin.reimbursements.index'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
