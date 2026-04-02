<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use App\Models\PayrollExport;
use App\Models\User;
use App\Models\Attendance;
use App\Helpers\PayrollHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        // NotificationService removed for better performance
    }

    /**
     * Display payroll management page
     */
    public function index(Request $request)
    {
        Log::info('Payroll index called', ['request' => $request->all()]);
        try {
            // Get filter parameters
            $filters = [
                'status' => $request->get('status'),
                'user_id' => $request->get('user_id'),
                'staff_name' => $request->get('staff_name'), // New: search by staff name
                'hospital' => $request->get('hospital'), // New: filter by hospital
                'search' => $request->get('search'),
                'week' => $request->get('week'), // Week filter (replaces period_start and period_end)
                'paid_by' => $request->get('paid_by'), // Filter by who paid the payroll
            ];

            // Build query - simplified to avoid issues
            $query = Payroll::query();

            // Filter by status
            if ($filters['status']) {
                $query->where('status', $filters['status']);
            }

            // Filter by hospital (alta/roxwood)
            if (!empty($filters['hospital'])) {
                try {
                    $hospital = $filters['hospital'];
                    $query->whereHas('user', function ($sub) use ($hospital) {
                        if ($hospital === 'roxwood') {
                            $sub->where('hospital', 'roxwood');
                        } elseif ($hospital === 'alta') {
                            $sub->where(function ($q) {
                                $q->where('hospital', 'alta')
                                    ->orWhereNull('hospital');
                            });
                        }
                    });
                } catch (\Exception $e) {
                    Log::warning('Error filtering by hospital', [
                        'hospital' => $filters['hospital'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Filter by user (keep for backward compatibility)
            if ($filters['user_id']) {
                $query->where('user_id', $filters['user_id']);
            }

            // Filter by staff name (search by name)
            if ($filters['staff_name']) {
                $staffName = trim($filters['staff_name']);
                $query->whereHas('user', function ($sub) use ($staffName) {
                    $sub->where('name', 'like', "%$staffName%");
                });
            }

            // Filter by week
            if (!empty($filters['week']) && $filters['week'] !== 'all') {
                try {
                    $weekDate = Carbon::parse($filters['week']);
                    $startOfWeek = $weekDate->copy()->startOfWeek();
                    $endOfWeek = $weekDate->copy()->endOfWeek();

                    $query->whereBetween('period_start', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('Error parsing week filter', [
                        'week' => $filters['week'],
                        'error' => $e->getMessage()
                    ]);
                    // If parsing fails, default to current week
                    $currentWeek = now()->startOfWeek();
                    $query->whereBetween('period_start', [
                        $currentWeek->format('Y-m-d'),
                        $currentWeek->copy()->endOfWeek()->format('Y-m-d')
                    ]);
                }
            } else if (empty($filters['week'])) {
                // Default: show current week if no week filter selected
                $currentWeek = now()->startOfWeek();
                $query->whereBetween('period_start', [
                    $currentWeek->format('Y-m-d'),
                    $currentWeek->copy()->endOfWeek()->format('Y-m-d')
                ]);
            }
            // If week = 'all', show all weeks (no additional filtering)

            // Search by user name or email (if staff_name not provided)
            if ($filters['search'] && !$filters['staff_name']) {
                $search = trim($filters['search']);
                $query->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                });
            }

            // Filter by who paid the payroll
            if (!empty($filters['paid_by']) && is_numeric($filters['paid_by'])) {
                $query->where('paid_by', (int) $filters['paid_by']);
            }

            // Get payrolls grouped by week
            $payrolls = collect([]);
            try {
                // Limit results if week=all to prevent memory issues
                if (!empty($filters['week']) && $filters['week'] === 'all') {
                    $payrollsCollection = $query->orderBy('period_start', 'desc')
                        ->orderBy('user_id')
                        ->limit(500) // Reduce limit to 500 records for better performance
                        ->get();
                } else {
                    $payrollsCollection = $query->orderBy('period_start', 'desc')
                        ->orderBy('user_id')
                        ->get();
                }

                // Load relationships separately to avoid N+1 but with error handling
                try {
                    $payrollsCollection->load([
                        'user' => function ($q) {
                            $q->select('id', 'name', 'email', 'staff_id');
                        },
                        'paidBy' => function ($q) {
                            $q->select('id', 'name');
                        }
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Error loading relationships', [
                        'error' => $e->getMessage()
                    ]);
                    // Try simple load
                    try {
                        $payrollsCollection->load(['user', 'paidBy']);
                    } catch (\Exception $e2) {
                        Log::error('Error loading relationships (simple)', [
                            'error' => $e2->getMessage()
                        ]);
                    }
                }

                // Group by week with error handling
                $groupedPayrolls = collect([]);
                foreach ($payrollsCollection as $payroll) {
                    try {
                        if (!$payroll || !$payroll->period_start) {
                            $weekKey = 'unknown';
                        } else {
                            $weekKey = Carbon::parse($payroll->period_start)->startOfWeek()->format('Y-m-d');
                        }

                        if (!$groupedPayrolls->has($weekKey)) {
                            $groupedPayrolls->put($weekKey, collect([]));
                        }
                        $groupedPayrolls->get($weekKey)->push($payroll);
                    } catch (\Exception $e) {
                        Log::warning('Error processing payroll in grouping', [
                            'payroll_id' => $payroll->id ?? 'unknown',
                            'period_start' => $payroll->period_start ?? 'null',
                            'error' => $e->getMessage()
                        ]);
                        if (!$groupedPayrolls->has('unknown')) {
                            $groupedPayrolls->put('unknown', collect([]));
                        }
                        $groupedPayrolls->get('unknown')->push($payroll);
                    }
                }
                $payrolls = $groupedPayrolls;
            } catch (\Exception $e) {
                Log::error('Error getting payrolls', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'filters' => $filters ?? [],
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $payrolls = collect([]);
            }

            // Get users for filter dropdown (kept for backward compatibility, but not used in new filter)
            try {
                $users = User::where('is_active', true)
                    ->whereHas('role')
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error getting users in payroll index', [
                    'error' => $e->getMessage()
                ]);
                $users = collect([]);
            }

            // Get users who have paid payrolls (for paid_by filter)
            try {
                $paidByUserIds = DB::table('payrolls')
                    ->whereNotNull('paid_by')
                    ->where('status', 'paid')
                    ->distinct()
                    ->pluck('paid_by')
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                $paidByUsers = collect([]);
                if (!empty($paidByUserIds)) {
                    $paidByUsers = User::whereIn('id', $paidByUserIds)
                        ->orderBy('name')
                        ->get();
                }
            } catch (\Exception $e) {
                Log::error('Error getting paidByUsers', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $paidByUsers = collect([]);
            }

            // Calculate summary statistics
            try {
                $summary = $this->calculateSummary($filters);
            } catch (\Exception $e) {
                Log::error('Error calculating summary', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $summary = [
                    'total_payrolls' => 0,
                    'pending_payrolls' => 0,
                    'paid_payrolls' => 0,
                    'total_amount' => 0,
                    'pending_amount' => 0,
                ];
            }

            // Get available weeks for navigation
            try {
                $availableWeeks = $this->getAvailableWeeks();
            } catch (\Exception $e) {
                Log::error('Error getting available weeks', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $availableWeeks = collect([]);
            }

            // Check if payroll for last week has been auto-generated
            // Auto-generate runs every Sunday at 23:59, so from Monday onwards, manual generate should be disabled
            $lastWeekStart = now()->subWeek()->startOfWeek();
            $lastWeekEnd = now()->subWeek()->endOfWeek();

            // Check if there are any payrolls for last week
            $lastWeekPayrollExists = Payroll::whereBetween('period_start', [
                $lastWeekStart->format('Y-m-d'),
                $lastWeekEnd->format('Y-m-d')
            ])->exists();

            // Disable manual generate if it's Monday or later and last week payroll already exists
            try {
                $isAfterSunday = now()->isMonday() || now()->isTuesday() || now()->isWednesday() ||
                    now()->isThursday() || now()->isFriday() || now()->isSaturday();
                // $canGenerateManually = !($isAfterSunday && $lastWeekPayrollExists);
                $canGenerateManually = true; // Allow manual generation to fix missing data
            } catch (\Exception $e) {
                Log::warning('Error determining canGenerateManually', [
                    'error' => $e->getMessage()
                ]);
                $canGenerateManually = true;
            }

            // Ensure all variables are defined before passing to view
            $payrolls = $payrolls ?? collect([]);
            $users = $users ?? collect([]);
            $paidByUsers = $paidByUsers ?? collect([]);
            $filters = $filters ?? [];
            $summary = $summary ?? [
                'total_payrolls' => 0,
                'pending_payrolls' => 0,
                'paid_payrolls' => 0,
                'total_amount' => 0,
                'pending_amount' => 0,
            ];
            $availableWeeks = $availableWeeks ?? collect([]);
            $canGenerateManually = $canGenerateManually ?? true;
            $lastWeekPayrollExists = $lastWeekPayrollExists ?? false;

            // FIX: Assign availableWeeks to weeks as well to prevent "Undefined variable $weeks" in view
            $weeks = $availableWeeks;

            // Check if export already done this month
            $currentMonthExport = PayrollExport::getCurrentMonthExport();

            return view('admin.payroll.index', compact(
                'payrolls',
                'users',
                'paidByUsers',
                'filters',
                'summary',
                'availableWeeks',
                'weeks', // Added this to fix the error from your log
                'canGenerateManually',
                'lastWeekPayrollExists',
                'currentMonthExport' // NEW: Monthly export tracking
            ));

        } catch (\Exception $e) {
            Log::error('Error in payroll index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
                'filters' => $filters ?? []
            ]);

            // Return error view instead of redirect to see the actual error in debug mode
            if (config('app.debug')) {
                throw $e;
            }

            return redirect()->route('admin.payroll.index')
                ->with('error', 'Terjadi kesalahan saat memuat data gaji. Silakan cek log untuk detail lebih lanjut.');
        }
    }

    /**
     * Get available weeks for navigation
     */
    private function getAvailableWeeks()
    {
        try {
            // Get distinct period_start dates
            $periodStarts = Payroll::whereNotNull('period_start')
                ->select('period_start')
                ->distinct()
                ->orderBy('period_start', 'desc')
                ->limit(100) // Get more to ensure we have enough weeks
                ->get()
                ->pluck('period_start')
                ->map(function ($date) {
                    try {
                        if (!$date) {
                            return null;
                        }
                        $carbonDate = Carbon::parse($date);
                        return $carbonDate->startOfWeek()->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->filter()
                ->unique()
                ->take(12) // Last 12 unique weeks
                ->map(function ($weekStart) {
                    try {
                        $weekStartCarbon = Carbon::parse($weekStart);
                        return [
                            'date' => $weekStartCarbon->format('Y-m-d'),
                            'label' => $weekStartCarbon->format('d M Y') . ' - ' . $weekStartCarbon->copy()->endOfWeek()->format('d M Y'),
                            'short_label' => 'Minggu ' . $weekStartCarbon->format('d M')
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Error formatting week in getAvailableWeeks', [
                            'week_start' => $weekStart,
                            'error' => $e->getMessage()
                        ]);
                        return null;
                    }
                })
                ->filter()
                ->values();

            return $periodStarts;
        } catch (\Exception $e) {
            Log::error('Error in getAvailableWeeks', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * Show payroll details
     */
    public function show(Payroll $payroll)
    {
        $payroll->load(['user.role', 'paidBy', 'notifications']);

        return view('admin.payroll.show', compact('payroll'));
    }

    /**
     * Generate payroll for a specific period
     */
    public function generate(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Check if last week payroll already exists (auto-generate runs on Sunday 23:59)
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        $lastWeekPayrollExists = Payroll::whereBetween('period_start', [
            $lastWeekStart->format('Y-m-d'),
            $lastWeekEnd->format('Y-m-d')
        ])->exists();

        // Disable manual generate if it's Monday or later and last week payroll already exists
        $isAfterSunday = now()->isMonday() || now()->isTuesday() || now()->isWednesday() ||
            now()->isThursday() || now()->isFriday() || now()->isSaturday();

        if ($isAfterSunday && $lastWeekPayrollExists) {
            return redirect()->back()->with('error', 'Generate gaji sudah dilakukan secara otomatis pada hari Minggu jam 23:59. Tidak dapat melakukan generate manual setelah auto-generate.');
        }

        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);
        $userIds = $request->user_ids;

        // Get users to process - ordered by name
        $users = User::where('is_active', true)
            ->whereHas('role')
            ->when($userIds, function ($query) use ($userIds) {
                return $query->whereIn('id', $userIds);
            })
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada staf yang dapat diproses untuk periode ini.');
        }

        $generatedCount = 0;
        $updatedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($users as $user) {
                // Check if payroll already exists for this period (overlapping check)
                // Use overlapping logic: (StartA <= EndB) and (EndA >= StartB)
                $existingPayroll = Payroll::where('user_id', $user->id)
                    ->where(function ($query) use ($periodStart, $periodEnd) {
                        $query->whereBetween('period_start', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
                            ->orWhereBetween('period_end', [$periodStart->format('Y-m-d'), $periodEnd->format('Y-m-d')])
                            ->orWhere(function ($q) use ($periodStart, $periodEnd) {
                                $q->where('period_start', '<=', $periodStart->format('Y-m-d'))
                                    ->where('period_end', '>=', $periodEnd->format('Y-m-d'));
                            });
                    })
                    ->first();

                // Get attendance data for the period
                // Only count valid work sessions (completed, with duration > 0)
                // Use toDateString() to ensure consistent date format
                $attendances = Attendance::where('user_id', $user->id)
                    ->whereBetween('work_date', [
                        $periodStart->toDateString(),
                        $periodEnd->toDateString()
                    ])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false) // Only completed sessions
                    ->get();

                if ($attendances->isEmpty()) {
                    $errors[] = "Tidak ada data absensi untuk {$user->name} pada periode ini.";
                    continue;
                }

                // Calculate total hours and salary
                $totalSeconds = $attendances->sum('session_duration');
                $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
                $roleName = optional($user->role)->name;
                $customSalary = $user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                // Log for debugging
                Log::info('Payroll generation calculation', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'period_start' => $periodStart->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                    'attendance_count' => $attendances->count(),
                    'total_seconds' => $totalSeconds,
                    'total_hours' => $totalHours,
                    'calculated_salary' => $calculatedSalary,
                    'existing_payroll_id' => $existingPayroll ? $existingPayroll->id : null,
                ]);

                // Skip if calculated salary is 0 or less
                if ($calculatedSalary <= 0) {
                    $errors[] = "Gaji untuk {$user->name} adalah Rp 0, dilewati.";
                    continue;
                }

                // Prepare payroll data
                $payrollData = [
                    'user_id' => $user->id,
                    'period_start' => $periodStart->format('Y-m-d'),
                    'period_end' => $periodEnd->format('Y-m-d'),
                    'total_hours' => $totalHours,
                    'base_salary' => $baseSalary,
                    'calculated_salary' => $calculatedSalary,
                    'notes' => "Generated manually on " . now()->format('Y-m-d H:i:s') . " for {$user->name}",
                ];

                if ($existingPayroll) {
                    // Check if payroll is already paid before update
                    $wasPaid = $existingPayroll->isPaid();

                    // Update existing payroll
                    // Preserve paid status, paid_at, and paid_by if already paid
                    if (!$wasPaid) {
                        $payrollData['status'] = 'pending';
                    } else {
                        // Remove status from update data if already paid to preserve it
                        unset($payrollData['status']);
                    }

                    $existingPayroll->update($payrollData);
                    $payroll = $existingPayroll->fresh(); // Refresh to get updated attributes

                    // Send notification when payroll is updated (only if not paid)
                    if (!$wasPaid) {
                        $this->sendSalaryPendingNotification($payroll, true);
                    }
                    $updatedCount++;
                } else {
                    // Create new payroll record
                    $payrollData['status'] = 'pending';
                    $payroll = Payroll::create($payrollData);

                    // Send notification when payroll is generated
                    $this->sendSalaryPendingNotification($payroll, false);
                    $generatedCount++;
                }
            }

            DB::commit();

            $message = "Berhasil generate {$generatedCount} gaji baru";
            if ($updatedCount > 0) {
                $message .= " dan update {$updatedCount} gaji yang sudah ada";
            }
            $message .= " untuk periode {$periodStart->format('d M Y')} - {$periodEnd->format('d M Y')}.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " error: " . implode(' ', $errors);
            }

            return redirect()->route('admin.payroll.index', ['week' => $periodStart->format('Y-m-d')])->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat generate gaji: ' . $e->getMessage());
        }
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(Request $request, Payroll $payroll)
    {
        if ($payroll->isPaid()) {
            return redirect()->back()->with('error', 'Gaji ini sudah dibayar.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Mark payroll as paid
            $payroll->markAsPaid(auth()->id());

            if ($request->notes) {
                $payroll->update(['notes' => $request->notes]);
            }

            // Send notification to user
            $this->sendSalaryPaidNotification($payroll);

            DB::commit();

            return redirect()->back()->with('success', 'Gaji berhasil ditandai sebagai dibayar dan notifikasi dikirim ke staf.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel payroll
     */
    public function cancel(Request $request, Payroll $payroll)
    {
        if ($payroll->isPaid()) {
            return redirect()->back()->with('error', 'Gaji yang sudah dibayar tidak dapat dibatalkan.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi.'
        ]);

        $payroll->markAsCancelled($request->cancel_reason);

        return redirect()->back()->with('success', 'Gaji berhasil dibatalkan.');
    }

    /**
     * Regenerate payroll - Recalculate with updated formula
     */
    public function regeneratePayroll(Payroll $payroll)
    {
        // Only allow regenerating pending payrolls
        if ($payroll->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya gaji dengan status pending yang bisa di-regenerate.');
        }

        DB::beginTransaction();
        try {
            // Recalculate based on current attendance
            $totalSeconds = $payroll->user->attendances()
                ->whereBetween('work_date', [$payroll->period_start, $payroll->period_end])
                ->whereIn('session_type', ['work', 'meeting'])
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0)
                ->where('is_active', false) // Only completed sessions
                ->sum('session_duration');

            $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
            $roleName = optional($payroll->user->role)->name;
            $customSalary = $payroll->user->custom_salary ?? 0;
            $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
            $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

            $oldSalary = $payroll->calculated_salary;

            // Update payroll
            $payroll->update([
                'total_hours' => $totalHours,
                'base_salary' => $baseSalary,
                'calculated_salary' => $calculatedSalary,
                'notes' => ($payroll->notes ? $payroll->notes . "\n" : '') .
                    "Regenerated on " . now()->format('Y-m-d H:i:s') .
                    " (Old: $" . number_format($oldSalary, 0, '.', ',') .
                    " → New: $" . number_format($calculatedSalary, 0, '.', ',') . ")"
            ]);

            DB::commit();

            return redirect()->back()->with(
                'success',
                "Gaji berhasil di-regenerate. Old: $" . number_format($oldSalary, 0, '.', ',') .
                " → New: $" . number_format($calculatedSalary, 0, '.', ',')
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error regenerating payroll', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat regenerate: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate all pending payrolls for a specific week
     */
    public function regenerateWeek(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $weekStart = Carbon::parse($request->week_start);
            $weekEnd = $weekStart->copy()->endOfWeek();

            // Get all pending payrolls for this week
            $payrolls = Payroll::where('status', 'pending')
                ->where('period_start', $weekStart->format('Y-m-d'))
                ->where('period_end', $weekEnd->format('Y-m-d'))
                ->get();

            if ($payrolls->isEmpty()) {
                return redirect()->back()->with('info', 'Tidak ada gaji pending untuk minggu ini.');
            }

            $regeneratedCount = 0;
            $totalOldSalary = 0;
            $totalNewSalary = 0;

            foreach ($payrolls as $payroll) {
                // Recalculate based on current attendance
                $totalSeconds = $payroll->user->attendances()
                    ->whereBetween('work_date', [$payroll->period_start, $payroll->period_end])
                    ->whereIn('session_type', ['work', 'meeting'])
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->where('is_active', false)
                    ->sum('session_duration');

                $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
                $roleName = optional($payroll->user->role)->name;
                $customSalary = $payroll->user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                $oldSalary = $payroll->calculated_salary;
                $totalOldSalary += $oldSalary;
                $totalNewSalary += $calculatedSalary;

                // Update payroll
                $payroll->update([
                    'total_hours' => $totalHours,
                    'base_salary' => $baseSalary,
                    'calculated_salary' => $calculatedSalary,
                    'notes' => ($payroll->notes ? $payroll->notes . "\n" : '') .
                        "Batch regenerated on " . now()->format('Y-m-d H:i:s') .
                        " (Old: $" . number_format($oldSalary, 0, '.', ',') .
                        " → New: $" . number_format($calculatedSalary, 0, '.', ',') . ")"
                ]);

                $regeneratedCount++;
            }

            DB::commit();

            return redirect()->back()->with(
                'success',
                "$regeneratedCount gaji berhasil di-regenerate. Total Old: $" . number_format($totalOldSalary, 0, '.', ',') .
                " → Total New: $" . number_format($totalNewSalary, 0, '.', ',')
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error regenerating week payrolls', [
                'week_start' => $request->week_start,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat regenerate: ' . $e->getMessage());
        }
    }

    /**
     * Delete payroll (for duplicate data)
     */
    public function destroy(Payroll $payroll)
    {
        if ($payroll->isPaid()) {
            return redirect()->back()->with('error', 'Gaji yang sudah dibayar tidak dapat dihapus.');
        }

        try {
            // Delete related notifications first
            $payroll->notifications()->delete();

            // Delete the payroll
            $payroll->delete();

            return redirect()->back()->with('success', 'Data gaji berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data gaji: ' . $e->getMessage());
        }
    }

    /**
     * Remove duplicate payrolls (keep only the latest)
     */
    public function removeDuplicates(Request $request)
    {
        try {
            $force = $request->has('force');

            // Find duplicates: same user_id, period_start, and period_end
            $duplicates = Payroll::select('user_id', 'period_start', 'period_end', DB::raw('COUNT(*) as count'))
                ->groupBy('user_id', 'period_start', 'period_end')
                ->having('count', '>', 1)
                ->get();

            if ($duplicates->isEmpty()) {
                return redirect()->back()->with('success', 'Tidak ada data gaji duplikat ditemukan.');
            }

            $totalDeleted = 0;
            $totalKept = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($duplicates as $duplicate) {
                    // Get all payrolls for this user and period
                    $payrolls = Payroll::where('user_id', $duplicate->user_id)
                        ->where('period_start', $duplicate->period_start)
                        ->where('period_end', $duplicate->period_end)
                        ->orderBy('created_at', 'desc') // Order by created_at descending (newest first)
                        ->orderBy('id', 'desc') // Also order by id as secondary sort
                        ->get();

                    if ($payrolls->count() <= 1) {
                        continue;
                    }

                    // Keep the first one (newest), delete the rest
                    $keepPayroll = $payrolls->first();
                    $deletePayrolls = $payrolls->skip(1);

                    foreach ($deletePayrolls as $payroll) {
                        // Check if payroll is paid
                        if ($payroll->isPaid() && !$force) {
                            continue;
                        }

                        try {
                            // Delete related notifications first
                            $payroll->notifications()->delete();

                            // Delete the payroll
                            $payroll->delete();
                            $totalDeleted++;
                        } catch (\Exception $e) {
                            $errors[] = "Error deleting Payroll ID {$payroll->id}: " . $e->getMessage();
                        }
                    }
                    $totalKept++;
                }

                DB::commit();

                $message = "Berhasil menghapus {$totalDeleted} data gaji duplikat. {$totalKept} data terbaru tetap disimpan.";
                if (!empty($errors)) {
                    $message .= " Terjadi " . count($errors) . " error.";
                }

                return redirect()->back()->with('success', $message);

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus duplikat: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Export payroll data
     */
    public function export(Request $request)
    {
        // Check if already exported this month
        if (PayrollExport::existsForCurrentMonth()) {
            $existingExport = PayrollExport::getCurrentMonthExport();

            return redirect()->back()->with('error', sprintf(
                'Data gaji sudah di export bulan ini oleh %s pada tanggal %s',
                optional($existingExport->exporter)->name ?? 'Unknown User',
                $existingExport->exported_at->format('d M Y H:i')
            ));
        }

        $filters = [
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id'),
            'staff_name' => $request->get('staff_name'),
            'hospital' => $request->get('hospital'),
            'week' => $request->get('week'),
            'paid_by' => $request->get('paid_by'),
        ];

        // Build query (same as index)
        $query = Payroll::with(['user', 'paidBy']);

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        // Filter by hospital
        if ($filters['hospital']) {
            $query->whereHas('user', function ($sub) use ($filters) {
                if ($filters['hospital'] === 'roxwood') {
                    // Roxwood: name atau staff_id mengandung "rh"
                    $sub->where(function ($q) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
                            ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh%']);
                    });
                } else if ($filters['hospital'] === 'alta') {
                    // Alta: name dan staff_id TIDAK mengandung "rh"
                    $sub->where(function ($q) {
                        $q->whereRaw('LOWER(name) NOT LIKE ?', ['%rh%'])
                            ->where(function ($sid) {
                                $sid->whereNull('staff_id')
                                    ->orWhereRaw('LOWER(staff_id) NOT LIKE ?', ['%rh%']);
                            });
                    });
                }
            });
        }

        if ($filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by staff name
        if ($filters['staff_name']) {
            $staffName = trim($filters['staff_name']);
            $query->whereHas('user', function ($sub) use ($staffName) {
                $sub->where('name', 'like', "%$staffName%");
            });
        }

        // Filter by week
        if ($filters['week'] && $filters['week'] !== 'all') {
            $weekDate = Carbon::parse($filters['week']);
            $startOfWeek = $weekDate->copy()->startOfWeek();
            $endOfWeek = $weekDate->copy()->endOfWeek();

            $query->whereBetween('period_start', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
        }
        // If week is not set or is 'all', export all data (no filter applied)

        // Filter by who paid the payroll
        if (!empty($filters['paid_by']) && is_numeric($filters['paid_by'])) {
            $query->where('paid_by', (int) $filters['paid_by']);
        }

        $payrolls = $query->orderBy('period_start', 'desc')->get();

        // Create export record BEFORE streaming
        // Wrapped in try-catch to handle race condition if 2 users export simultaneously
        try {
            PayrollExport::create([
                'export_year' => now()->year,
                'export_month' => now()->month,
                'exported_by' => auth()->id(),
                'exported_at' => now(),
                'filters' => $filters,
                'records_count' => $payrolls->count(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate key error (unique constraint violation)
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->back()->with(
                    'error',
                    'Data gaji sudah di export bulan ini oleh user lain. Silakan refresh halaman.'
                );
            }
            throw $e; // Re-throw if it's a different error
        }

        $filename = 'payroll_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Nama Staf',
                'Email',
                'Periode Mulai',
                'Periode Akhir',
                'Total Jam',
                'Gaji Pokok',
                'Gaji Dihitung',
                'Status',
                'Tanggal Dibayar',
                'Dibayar Oleh',
                'Catatan'
            ]);

            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->user->name,
                    $payroll->user->email,
                    $payroll->period_start->format('Y-m-d'),
                    $payroll->period_end->format('Y-m-d'),
                    $payroll->formatted_hours,
                    PayrollHelper::formatCurrency($payroll->base_salary),
                    $payroll->formatted_salary,
                    ucfirst($payroll->status),
                    $payroll->paid_at ? $payroll->paid_at->format('Y-m-d H:i:s') : '-',
                    $payroll->paidBy ? $payroll->paidBy->name : '-',
                    $payroll->notes ?? '-'
                ]);
            }

            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($filters)
    {
        $query = Payroll::query();

        // Apply same filters as main query
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        if ($filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        // Filter by hospital
        if ($filters['hospital']) {
            $query->whereHas('user', function ($sub) use ($filters) {
                if ($filters['hospital'] === 'roxwood') {
                    // Roxwood: name atau staff_id mengandung "rh"
                    $sub->where(function ($q) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%rh%'])
                            ->orWhereRaw('LOWER(staff_id) LIKE ?', ['%rh%']);
                    });
                } else if ($filters['hospital'] === 'alta') {
                    // Alta: name dan staff_id TIDAK mengandung "rh"
                    $sub->where(function ($q) {
                        $q->whereRaw('LOWER(name) NOT LIKE ?', ['%rh%'])
                            ->where(function ($sid) {
                                $sid->whereNull('staff_id')
                                    ->orWhereRaw('LOWER(staff_id) NOT LIKE ?', ['%rh%']);
                            });
                    });
                }
            });
        }

        // Filter by staff name
        if ($filters['staff_name']) {
            $staffName = trim($filters['staff_name']);
            $query->whereHas('user', function ($sub) use ($staffName) {
                $sub->where('name', 'like', "%$staffName%");
            });
        }

        // Filter by week
        if ($filters['week'] && $filters['week'] !== 'all') {
            $weekDate = Carbon::parse($filters['week']);
            $startOfWeek = $weekDate->copy()->startOfWeek();
            $endOfWeek = $weekDate->copy()->endOfWeek();

            $query->whereBetween('period_start', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
        } else if (!$filters['week']) {
            // Default: show current week if no week filter selected
            $currentWeek = now()->startOfWeek();
            $query->whereBetween('period_start', [
                $currentWeek->format('Y-m-d'),
                $currentWeek->copy()->endOfWeek()->format('Y-m-d')
            ]);
        }

        // Filter by who paid the payroll
        if (!empty($filters['paid_by']) && is_numeric($filters['paid_by'])) {
            $query->where('paid_by', (int) $filters['paid_by']);
        }

        return [
            'total_payrolls' => $query->count(),
            'pending_payrolls' => (clone $query)->where('status', 'pending')->count(),
            'paid_payrolls' => (clone $query)->where('status', 'paid')->count(),
            'total_amount' => (clone $query)->where('status', 'paid')->sum('calculated_salary'),
            'pending_amount' => (clone $query)->where('status', 'pending')->sum('calculated_salary'),
        ];
    }

    /**
     * Send salary pending notification when payroll is generated or updated
     */
    private function sendSalaryPendingNotification(Payroll $payroll, $isUpdate = false)
    {
        try {
            // Create notification record
            $message = $isUpdate
                ? "Gaji Anda untuk periode {$payroll->period_description} telah di-update sebesar {$payroll->formatted_salary}. Status: Pending"
                : "Gaji Anda untuk periode {$payroll->period_description} telah di-generate sebesar {$payroll->formatted_salary}. Status: Pending";

            $notification = PayrollNotification::create([
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'notification_type' => 'salary_pending',
                'message' => $message,
                'metadata' => [
                    'amount' => $payroll->calculated_salary,
                    'period' => $payroll->period_description,
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'is_update' => $isUpdate,
                ],
            ]);

            // Mark notification as sent (since we're just creating the record)
            $notification->markAsSent();

        } catch (\Exception $e) {
            Log::error('Failed to send salary pending notification', [
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'error' => $e->getMessage()
            ]);

            // Mark notification as failed if there's an exception
            if (isset($notification)) {
                $notification->markAsFailed();
            }
        }
    }

    /**
     * Send salary paid notification
     */
    private function sendSalaryPaidNotification(Payroll $payroll)
    {
        try {
            // Create notification record
            $notification = PayrollNotification::create([
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'notification_type' => 'salary_paid',
                'message' => "Gaji Anda untuk periode {$payroll->period_description} telah dibayar sebesar {$payroll->formatted_salary}",
                'metadata' => [
                    'amount' => $payroll->calculated_salary,
                    'period' => $payroll->period_description,
                    'paid_at' => $payroll->paid_at->format('Y-m-d H:i:s'),
                ],
            ]);

            // Mark notification as sent (NotificationService removed)
            $notification->markAsSent();

        } catch (\Exception $e) {
            Log::error('Failed to send salary paid notification', [
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'error' => $e->getMessage()
            ]);

            // Mark notification as failed if there's an exception
            if (isset($notification)) {
                $notification->markAsFailed();
            }
        }
    }
}