<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Display staff's payroll page
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get filter parameters
            $filters = [
                'status' => $request->get('status'),
                'period_start' => $request->get('period_start'),
                'period_end' => $request->get('period_end'),
            ];

            // Build query for user's payrolls
            $query = Payroll::where('user_id', $user->id);

            // Filter by status
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Filter by period
            if (!empty($filters['period_start'])) {
                $query->where('period_start', '>=', $filters['period_start']);
            }
            if (!empty($filters['period_end'])) {
                $query->where('period_end', '<=', $filters['period_end']);
            }

            // Get payrolls with eager loading
            // Use left join to handle null paidBy gracefully
            $payrolls = $query->with(['paidBy' => function($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('period_start', 'desc')
                ->paginate(15);

            // Calculate summary statistics
            $summary = $this->calculateSummary($user->id, $filters);

            // Get recent notifications
            $recentNotifications = PayrollNotification::where('user_id', $user->id)
                ->where('status', 'sent')
                ->whereNotNull('sent_at')
                ->orderBy('sent_at', 'desc')
                ->limit(5)
                ->get();

            return view('staff.payroll.index', compact('payrolls', 'filters', 'summary', 'recentNotifications'));
        } catch (\Exception $e) {
            \Log::error('Error in staff payroll index', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('staff.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat data gaji. Silakan coba lagi.');
        }
    }

    /**
     * Show payroll details
     * Note: Route model binding already filters by user_id via resolveRouteBinding
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            // Manually find payroll with user constraint
            $payroll = Payroll::where('id', $id)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$payroll) {
                \Log::warning('Payroll not found or unauthorized access attempt', [
                    'payroll_id' => $id,
                    'user_id' => $user->id,
                ]);
                return redirect()->route('staff.payroll.index')
                    ->with('error', 'Gaji tidak ditemukan atau Anda tidak memiliki akses.');
            }

            $payroll->load(['user.role', 'paidBy', 'notifications']);
            
            return view('staff.payroll.show', compact('payroll'));
        } catch (\Exception $e) {
            \Log::error('Error in staff payroll show', [
                'payroll_id' => $id ?? null,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('staff.payroll.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail gaji. Silakan coba lagi.');
        }
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(PayrollNotification $notification)
    {
        // Ensure user can only mark their own notifications
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk notifikasi ini.');
        }

        // For now, we'll just return success
        // In a more complex system, you might want to track read status
        return response()->json(['success' => true]);
    }

    /**
     * Get payroll statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'month'); // week, month, year
        
        $query = Payroll::where('user_id', $user->id);
        
        switch ($period) {
            case 'week':
                $query->whereBetween('period_start', [
                    now()->startOfWeek()->toDateString(), 
                    now()->endOfWeek()->toDateString()
                ]);
                break;
            case 'month':
                $query->whereBetween('period_start', [
                    now()->startOfMonth()->toDateString(), 
                    now()->endOfMonth()->toDateString()
                ]);
                break;
            case 'year':
                $query->whereBetween('period_start', [
                    now()->startOfYear()->toDateString(), 
                    now()->endOfYear()->toDateString()
                ]);
                break;
        }

        $payrolls = $query->get();
        
        $stats = [
            'total_payrolls' => $payrolls->count(),
            'paid_payrolls' => $payrolls->where('status', 'paid')->count(),
            'pending_payrolls' => $payrolls->where('status', 'pending')->count(),
            'total_amount' => $payrolls->where('status', 'paid')->sum('calculated_salary') ?? 0,
            'pending_amount' => $payrolls->where('status', 'pending')->sum('calculated_salary') ?? 0,
            'period' => $period
        ];

        return response()->json($stats);
    }

    /**
     * Calculate summary statistics for staff
     */
    private function calculateSummary($userId, $filters)
    {
        $query = Payroll::where('user_id', $userId);

        // Apply same filters as main query
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['period_start'])) {
            $query->where('period_start', '>=', $filters['period_start']);
        }
        if (!empty($filters['period_end'])) {
            $query->where('period_end', '<=', $filters['period_end']);
        }

        $allPayrolls = $query->get();

        return [
            'total_payrolls' => $allPayrolls->count(),
            'paid_payrolls' => $allPayrolls->where('status', 'paid')->count(),
            'pending_payrolls' => $allPayrolls->where('status', 'pending')->count(),
            'total_amount' => $allPayrolls->where('status', 'paid')->sum('calculated_salary') ?? 0,
            'pending_amount' => $allPayrolls->where('status', 'pending')->sum('calculated_salary') ?? 0,
            'average_salary' => $allPayrolls->where('status', 'paid')->avg('calculated_salary') ?? 0,
            'highest_salary' => $allPayrolls->where('status', 'paid')->max('calculated_salary') ?? 0,
        ];
    }
}
