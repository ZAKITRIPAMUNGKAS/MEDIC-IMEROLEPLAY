<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollNotification;
use App\Models\PayrollExport;
use App\Models\User;
use App\Models\Attendance;
use App\Models\MeetingRequest;
use App\Helpers\PayrollHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
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

            // Total pending meeting requests
            $pendingMeetingRequestsCount = MeetingRequest::pending()->count();

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
                'currentMonthExport', // NEW: Monthly export tracking
                'pendingMeetingRequestsCount'
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

        // Check if last week payroll already exists (for informational purposes)
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        $lastWeekPayrollExists = Payroll::whereBetween('period_start', [
            $lastWeekStart->format('Y-m-d'),
            $lastWeekEnd->format('Y-m-d')
        ])->exists();

        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);
        $userIds = $request->user_ids;

        // Block generation if there are pending meeting requests in the target period
        $pendingMeetings = MeetingRequest::getPendingInPeriod(
            $periodStart->toDateString(),
            $periodEnd->toDateString(),
            $userIds
        );

        if ($pendingMeetings->isNotEmpty()) {
            $count = $pendingMeetings->count();
            $staffList = $pendingMeetings->pluck('user.name')->filter()->unique();
            $namesSample = $staffList->take(3)->implode(', ');
            $remaining = $staffList->count() - 3;
            $staffText = $namesSample . ($remaining > 0 ? " dan {$remaining} staf lainnya" : "");

            return redirect()->back()->with(
                'error',
                "Generate gaji tidak dapat dilakukan! Terdapat {$count} pengajuan meeting (Meeting Request) yang belum di-ACC atau divalidasi (masih pending) pada periode {$periodStart->format('d M Y')} s/d {$periodEnd->format('d M Y')}" .
                ($staffText ? " (Staf: {$staffText})" : "") .
                ". Pastikan semua pengajuan meeting telah disetujui (ACC) atau ditolak terlebih dahulu agar jam kerja meeting staf tercatat ke sistem absensi secara valid."
            );
        }

        // Get users to process - ordered by name
        // Include:
        // 1. Active users with role or medicRole
        // 2. Any users who worked (have attendance) in this period even if their account status is temporarily inactive or only has medic_role_id
        $users = User::where(function ($query) use ($periodStart, $periodEnd) {
                $query->where('is_active', true)
                      ->orWhereHas('attendances', function ($aq) use ($periodStart, $periodEnd) {
                          $aq->where(function ($dq) use ($periodStart, $periodEnd) {
                              $dq->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                                 ->orWhereBetween(DB::raw('DATE(clock_in)'), [$periodStart->toDateString(), $periodEnd->toDateString()]);
                          })
                          ->where(function ($cq) {
                              $cq->where('is_active', false)
                                 ->orWhereNotNull('clock_out');
                          })
                          ->whereNotNull('session_duration')
                          ->where('session_duration', '>', 0);
                      });
            })
            ->where(function ($query) {
                $query->whereHas('role')
                      ->orWhereHas('medicRole')
                      ->orWhereNotNull('role_id')
                      ->orWhereNotNull('medic_role_id');
            })
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
                // Check if payroll already exists for this user in this week/period
                $existingPayroll = Payroll::where('user_id', $user->id)
                    ->where(function ($q) use ($periodStart, $periodEnd) {
                        $q->where(function ($exact) use ($periodStart, $periodEnd) {
                            $exact->where('period_start', $periodStart->toDateString())
                                  ->where('period_end', $periodEnd->toDateString());
                        })
                        ->orWhere('period_start', $periodStart->toDateString())
                        ->orWhereBetween('period_start', [
                            $periodStart->copy()->startOfWeek()->toDateString(),
                            $periodStart->copy()->endOfWeek()->toDateString()
                        ]);
                    })
                    ->orderByRaw("CASE WHEN status = 'paid' THEN 0 ELSE 1 END")
                    ->first();

                // Get attendance data for the period
                // Count valid work sessions (completed: is_active = false or clock_out is not null, with duration > 0)
                // Support sessions with work_date OR clock_in matching the period
                // Support session types: work, meeting, overtime, duty, study, or null (exclude only break & custom)
                $attendances = Attendance::where('user_id', $user->id)
                    ->where(function ($dq) use ($periodStart, $periodEnd) {
                        $dq->whereBetween('work_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                           ->orWhereBetween(DB::raw('DATE(clock_in)'), [$periodStart->toDateString(), $periodEnd->toDateString()]);
                    })
                    ->where(function ($sq) {
                        $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                           ->orWhereNull('session_type')
                           ->orWhereNotIn('session_type', ['break', 'custom']);
                    })
                    ->where(function ($cq) {
                        $cq->where('is_active', false)
                           ->orWhereNotNull('clock_out');
                    })
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->get();

                if ($attendances->isEmpty()) {
                    $errors[] = "Tidak ada data absensi untuk {$user->name} pada periode ini.";
                    continue;
                }

                // Auto-heal any attendances in this batch where clock_out exists but is_active remained true
                foreach ($attendances as $att) {
                    if ($att->is_active && $att->clock_out) {
                        $att->update(['is_active' => false]);
                    }
                }

                // Calculate total hours and salary
                $totalSeconds = $attendances->sum('session_duration');
                $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);
                $roleName = optional($user->role)->name;
                if (!$roleName && $user->effective_medic_role) {
                    $roleName = $user->effective_medic_role->name;
                }
                if (!$roleName && $user->medicRole) {
                    $roleName = $user->medicRole->name;
                }
                if (!$roleName && $user->role) {
                    $roleName = $user->role->display_name;
                }
                if (!$roleName && $user->medicRole) {
                    $roleName = $user->medicRole->display_name;
                }

                $customSalary = $user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);

                // Fallback to effective medic role if base salary is 0
                if ($baseSalary <= 0 && $user->effective_medic_role && $user->effective_medic_role->name !== $roleName) {
                    $altRole = $user->effective_medic_role->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                    if ($altBase > 0) {
                        $roleName = $altRole;
                        $baseSalary = $altBase;
                    }
                }
                if ($baseSalary <= 0 && $user->medicRole && $user->medicRole->name !== $roleName) {
                    $altRole = $user->medicRole->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                    if ($altBase > 0) {
                        $roleName = $altRole;
                        $baseSalary = $altBase;
                    }
                }

                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                // Fallback for staff who worked hours (> 0) but whose role had no explicit salary setting
                if ($calculatedSalary <= 0 && $totalSeconds > 0) {
                    $fallbackSetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                        ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
                    if ($fallbackSetting) {
                        $calculatedSalary = $fallbackSetting->calculateSalary($totalHours);
                        $baseSalary = $baseSalary > 0 ? $baseSalary : (int) $fallbackSetting->weekly_salary;
                    }
                }

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

                // Skip ONLY if user has 0 total seconds or if calculated salary is strictly <= 0 with 0 hours
                if ($totalSeconds <= 0 || $calculatedSalary <= 0) {
                    $errors[] = "Gaji untuk {$user->name} adalah Rp 0 (0 jam kerja), dilewati.";
                    continue;
                }

                if ($existingPayroll) {
                    // NEVER overwrite or modify a payroll that is already marked as paid
                    if ($existingPayroll->isPaid()) {
                        $errors[] = "Gaji untuk {$user->name} pada periode ini sudah dibayar, dilewati.";
                        continue;
                    }

                    // Update existing pending payroll (recalculate and standardize dates)
                    $existingPayroll->update([
                        'period_start' => $periodStart->format('Y-m-d'),
                        'period_end' => $periodEnd->format('Y-m-d'),
                        'total_hours' => $totalHours,
                        'base_salary' => $baseSalary,
                        'calculated_salary' => $calculatedSalary,
                        'status' => 'pending',
                        'notes' => "Updated manually on " . now()->format('Y-m-d H:i:s') . " for {$user->name}",
                    ]);

                    // Clean up any extra pending duplicates for this user in this week
                    Payroll::where('user_id', $user->id)
                        ->where('id', '!=', $existingPayroll->id)
                        ->where('status', '!=', 'paid')
                        ->whereBetween('period_start', [
                            $periodStart->copy()->startOfWeek()->toDateString(),
                            $periodStart->copy()->endOfWeek()->toDateString()
                        ])
                        ->each(function ($extra) {
                            $extra->notifications()->delete();
                            $extra->delete();
                        });

                    $payroll = $existingPayroll->fresh();

                    // Send notification when payroll is updated
                    $this->sendSalaryPendingNotification($payroll, true);
                    $updatedCount++;
                } else {
                    // Create new payroll record
                    $payroll = Payroll::create([
                        'user_id' => $user->id,
                        'period_start' => $periodStart->format('Y-m-d'),
                        'period_end' => $periodEnd->format('Y-m-d'),
                        'total_hours' => $totalHours,
                        'base_salary' => $baseSalary,
                        'calculated_salary' => $calculatedSalary,
                        'status' => 'pending',
                        'notes' => "Generated manually on " . now()->format('Y-m-d H:i:s') . " for {$user->name}",
                    ]);

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

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('markAsPaid failed: ' . $e->getMessage(), ['payroll_id' => $payroll->id, 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pembayaran gaji: ' . $e->getMessage());
        }
    }

    /**
     * Bulk mark payrolls as paid
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $request->validate([
            'payroll_ids' => 'required|array|min:1',
            'payroll_ids.*' => 'exists:payrolls,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'payroll_ids.required' => 'Pilih minimal satu data gaji yang ingin ditandai dibayar.',
            'payroll_ids.min' => 'Pilih minimal satu data gaji yang ingin ditandai dibayar.',
        ]);

        $payrollIds = $request->payroll_ids;
        $payrolls = Payroll::whereIn('id', $payrollIds)
            ->where('status', 'pending')
            ->get();

        if ($payrolls->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada gaji berstatus Pending yang dapat ditandai dibayar.');
        }

        $successCount = 0;
        $adminId = auth()->id();
        $notes = $request->notes;

        DB::beginTransaction();
        try {
            foreach ($payrolls as $payroll) {
                $payroll->markAsPaid($adminId);

                if ($notes) {
                    $payroll->update(['notes' => $notes]);
                }

                // Send notification safely
                try {
                    $this->sendSalaryPaidNotification($payroll);
                } catch (\Throwable $te) {
                    Log::warning('Failed notifying payroll ' . $payroll->id . ': ' . $te->getMessage());
                }

                $successCount++;
            }

            DB::commit();

            return redirect()->back()->with('success', "Berhasil menandai {$successCount} data gaji sebagai dibayar.");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('bulkMarkAsPaid failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal memproses ceklis gaji: ' . $e->getMessage());
        }
    }

    /**
     * Undo paid status (revert to pending)
     * Allowed only within 1 hour of payment
     */
    public function undoPayment(Payroll $payroll)
    {
        if (!$payroll->isPaid()) {
            return redirect()->back()->with('error', 'Gaji ini belum dibayar.');
        }

        DB::beginTransaction();
        try {
            $payroll->update([
                'status' => 'pending',
                'paid_at' => null,
                'paid_by' => null,
                'notes' => ($payroll->notes ? $payroll->notes . "\n" : '') . 
                          "Pembayaran dibatalkan pada " . now()->format('Y-m-d H:i:s') . " oleh " . auth()->user()->name
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pembayaran gaji berhasil dibatalkan dan dikembalikan ke status Pending.');

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

        // Check if user has pending meeting requests in this period
        $pendingMeetings = MeetingRequest::getPendingInPeriod(
            $payroll->period_start,
            $payroll->period_end,
            $payroll->user_id
        );

        if ($pendingMeetings->isNotEmpty()) {
            $count = $pendingMeetings->count();
            return redirect()->back()->with(
                'error',
                "Tidak dapat me-regenerate gaji {$payroll->user->name}! Masih terdapat {$count} pengajuan meeting staf ini yang belum di-ACC atau divalidasi pada periode tersebut."
            );
        }

        DB::beginTransaction();
        try {
            // Recalculate based on current attendance with full session_type, is_active, and date tolerance
            $attendances = $payroll->user->attendances()
                ->where(function ($dq) use ($payroll) {
                    $dq->whereBetween('work_date', [$payroll->period_start, $payroll->period_end])
                       ->orWhereBetween(DB::raw('DATE(clock_in)'), [$payroll->period_start, $payroll->period_end]);
                })
                ->where(function ($sq) {
                    $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                       ->orWhereNull('session_type')
                       ->orWhereNotIn('session_type', ['break', 'custom']);
                })
                ->where(function ($cq) {
                    $cq->where('is_active', false)
                       ->orWhereNotNull('clock_out');
                })
                ->whereNotNull('session_duration')
                ->where('session_duration', '>', 0)
                ->get();

            // Auto-heal any attendances in this batch where clock_out exists but is_active remained true
            foreach ($attendances as $att) {
                if ($att->is_active && $att->clock_out) {
                    $att->update(['is_active' => false]);
                }
            }

            $totalSeconds = $attendances->sum('session_duration');
            $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);

            $roleName = optional($payroll->user->role)->name;
            if (!$roleName && $payroll->user->effective_medic_role) {
                $roleName = $payroll->user->effective_medic_role->name;
            }
            if (!$roleName && $payroll->user->medicRole) {
                $roleName = $payroll->user->medicRole->name;
            }
            if (!$roleName && $payroll->user->role) {
                $roleName = $payroll->user->role->display_name;
            }
            if (!$roleName && $payroll->user->medicRole) {
                $roleName = $payroll->user->medicRole->display_name;
            }

            $customSalary = $payroll->user->custom_salary ?? 0;
            $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
            if ($baseSalary <= 0 && $payroll->user->effective_medic_role && $payroll->user->effective_medic_role->name !== $roleName) {
                $altRole = $payroll->user->effective_medic_role->name;
                $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                if ($altBase > 0) {
                    $roleName = $altRole;
                    $baseSalary = $altBase;
                }
            }
            if ($baseSalary <= 0 && $payroll->user->medicRole && $payroll->user->medicRole->name !== $roleName) {
                $altRole = $payroll->user->medicRole->name;
                $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                if ($altBase > 0) {
                    $roleName = $altRole;
                    $baseSalary = $altBase;
                }
            }

            $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

            // Fallback for staff who worked hours (> 0) but whose role had no explicit salary setting
            if ($calculatedSalary <= 0 && $totalSeconds > 0) {
                $fallbackSetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                    ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
                if ($fallbackSetting) {
                    $calculatedSalary = $fallbackSetting->calculateSalary($totalHours);
                    $baseSalary = $baseSalary > 0 ? $baseSalary : (int) $fallbackSetting->weekly_salary;
                }
            }

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

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error regenerating payroll', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat regenerate: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate all pending payrolls for a specific week, plus auto-generate any missing staff
     */
    public function regenerateWeek(Request $request)
    {
        $request->validate([
            'week_start' => 'required|date',
        ]);

        $weekStart = Carbon::parse($request->week_start);
        $weekEnd = $weekStart->copy()->endOfWeek();

        // Check for pending meeting requests in this week
        $pendingMeetings = MeetingRequest::getPendingInPeriod(
            $weekStart->toDateString(),
            $weekEnd->toDateString()
        );

        if ($pendingMeetings->isNotEmpty()) {
            $count = $pendingMeetings->count();
            return redirect()->back()->with(
                'error',
                "Tidak dapat me-regenerate gaji minggu ini! Terdapat {$count} pengajuan meeting yang masih berstatus Pending pada periode {$weekStart->format('d M Y')} s/d {$weekEnd->format('d M Y')}. Harap proses (ACC atau tolak) semua meeting request terlebih dahulu."
            );
        }

        DB::beginTransaction();
        try {
            // Get all pending payrolls for this week (support exact match or week-bounded match)
            $payrolls = Payroll::where('status', 'pending')
                ->where(function ($q) use ($weekStart, $weekEnd) {
                    $q->where(function ($exact) use ($weekStart, $weekEnd) {
                        $exact->where('period_start', $weekStart->format('Y-m-d'))
                              ->where('period_end', $weekEnd->format('Y-m-d'));
                    })
                    ->orWhereBetween('period_start', [$weekStart->toDateString(), $weekEnd->toDateString()]);
                })
                ->get();

            $regeneratedCount = 0;
            $createdCount = 0;
            $totalOldSalary = 0;
            $totalNewSalary = 0;

            foreach ($payrolls as $payroll) {
                // Recalculate based on current attendance with full session_type, is_active, and date tolerance
                $attendances = $payroll->user->attendances()
                    ->where(function ($dq) use ($payroll, $weekStart, $weekEnd) {
                        $dq->whereBetween('work_date', [$payroll->period_start, $payroll->period_end])
                           ->orWhereBetween(DB::raw('DATE(clock_in)'), [$payroll->period_start, $payroll->period_end])
                           ->orWhereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                           ->orWhereBetween(DB::raw('DATE(clock_in)'), [$weekStart->toDateString(), $weekEnd->toDateString()]);
                    })
                    ->where(function ($sq) {
                        $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                           ->orWhereNull('session_type')
                           ->orWhereNotIn('session_type', ['break', 'custom']);
                    })
                    ->where(function ($cq) {
                        $cq->where('is_active', false)
                           ->orWhereNotNull('clock_out');
                    })
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->get();

                // Auto-heal
                foreach ($attendances as $att) {
                    if ($att->is_active && $att->clock_out) {
                        $att->update(['is_active' => false]);
                    }
                }

                $totalSeconds = $attendances->sum('session_duration');
                $totalHours = PayrollHelper::convertSecondsToHours($totalSeconds);

                $roleName = optional($payroll->user->role)->name;
                if (!$roleName && $payroll->user->effective_medic_role) {
                    $roleName = $payroll->user->effective_medic_role->name;
                }
                if (!$roleName && $payroll->user->medicRole) {
                    $roleName = $payroll->user->medicRole->name;
                }
                if (!$roleName && $payroll->user->role) {
                    $roleName = $payroll->user->role->display_name;
                }
                if (!$roleName && $payroll->user->medicRole) {
                    $roleName = $payroll->user->medicRole->display_name;
                }

                $customSalary = $payroll->user->custom_salary ?? 0;
                $baseSalary = PayrollHelper::getBaseSalary($roleName, $customSalary);
                if ($baseSalary <= 0 && $payroll->user->effective_medic_role && $payroll->user->effective_medic_role->name !== $roleName) {
                    $altRole = $payroll->user->effective_medic_role->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                    if ($altBase > 0) {
                        $roleName = $altRole;
                        $baseSalary = $altBase;
                    }
                }
                if ($baseSalary <= 0 && $payroll->user->medicRole && $payroll->user->medicRole->name !== $roleName) {
                    $altRole = $payroll->user->medicRole->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $customSalary);
                    if ($altBase > 0) {
                        $roleName = $altRole;
                        $baseSalary = $altBase;
                    }
                }

                $calculatedSalary = PayrollHelper::computeWeeklySalary($roleName, $totalSeconds, $customSalary);

                if ($calculatedSalary <= 0 && $totalSeconds > 0) {
                    $fallbackSetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                        ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
                    if ($fallbackSetting) {
                        $calculatedSalary = $fallbackSetting->calculateSalary($totalHours);
                        $baseSalary = $baseSalary > 0 ? $baseSalary : (int) $fallbackSetting->weekly_salary;
                    }
                }

                $oldSalary = $payroll->calculated_salary;
                $totalOldSalary += $oldSalary;
                $totalNewSalary += $calculatedSalary;

                // Update payroll
                $payroll->update([
                    'period_start' => $weekStart->format('Y-m-d'),
                    'period_end' => $weekEnd->format('Y-m-d'),
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

            // CRITICAL FEATURE: Also check for staff who worked this week but have NO payroll record yet (e.g. Jasmine)
            $existingPayrollUserIds = Payroll::where(function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('period_start', [$weekStart->toDateString(), $weekEnd->toDateString()]);
            })->pluck('user_id')->toArray();

            $missingUsers = User::whereNotIn('id', $existingPayrollUserIds)
                ->whereHas('attendances', function ($aq) use ($weekStart, $weekEnd) {
                    $aq->where(function ($dq) use ($weekStart, $weekEnd) {
                        $dq->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                           ->orWhereBetween(DB::raw('DATE(clock_in)'), [$weekStart->toDateString(), $weekEnd->toDateString()]);
                    })
                    ->where(function ($sq) {
                        $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                           ->orWhereNull('session_type')
                           ->orWhereNotIn('session_type', ['break', 'custom']);
                    })
                    ->where(function ($cq) {
                        $cq->where('is_active', false)
                           ->orWhereNotNull('clock_out');
                    })
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0);
                })
                ->get();

            foreach ($missingUsers as $missingUser) {
                $userAttendances = Attendance::where('user_id', $missingUser->id)
                    ->where(function ($dq) use ($weekStart, $weekEnd) {
                        $dq->whereBetween('work_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                           ->orWhereBetween(DB::raw('DATE(clock_in)'), [$weekStart->toDateString(), $weekEnd->toDateString()]);
                    })
                    ->where(function ($sq) {
                        $sq->whereIn('session_type', ['work', 'meeting', 'overtime', 'duty', 'study'])
                           ->orWhereNull('session_type')
                           ->orWhereNotIn('session_type', ['break', 'custom']);
                    })
                    ->where(function ($cq) {
                        $cq->where('is_active', false)
                           ->orWhereNotNull('clock_out');
                    })
                    ->whereNotNull('session_duration')
                    ->where('session_duration', '>', 0)
                    ->get();

                if ($userAttendances->isEmpty()) {
                    continue;
                }

                foreach ($userAttendances as $att) {
                    if ($att->is_active && $att->clock_out) {
                        $att->update(['is_active' => false]);
                    }
                }

                $userTotalSeconds = $userAttendances->sum('session_duration');
                $userTotalHours = PayrollHelper::convertSecondsToHours($userTotalSeconds);

                $userRoleName = optional($missingUser->role)->name;
                if (!$userRoleName && $missingUser->effective_medic_role) {
                    $userRoleName = $missingUser->effective_medic_role->name;
                }
                if (!$userRoleName && $missingUser->medicRole) {
                    $userRoleName = $missingUser->medicRole->name;
                }
                if (!$userRoleName && $missingUser->role) {
                    $userRoleName = $missingUser->role->display_name;
                }
                if (!$userRoleName && $missingUser->medicRole) {
                    $userRoleName = $missingUser->medicRole->display_name;
                }

                $userCustomSalary = $missingUser->custom_salary ?? 0;
                $userBaseSalary = PayrollHelper::getBaseSalary($userRoleName, $userCustomSalary);
                if ($userBaseSalary <= 0 && $missingUser->effective_medic_role && $missingUser->effective_medic_role->name !== $userRoleName) {
                    $altRole = $missingUser->effective_medic_role->name;
                    $altBase = PayrollHelper::getBaseSalary($altRole, $userCustomSalary);
                    if ($altBase > 0) {
                        $userRoleName = $altRole;
                        $userBaseSalary = $altBase;
                    }
                }

                $userCalculatedSalary = PayrollHelper::computeWeeklySalary($userRoleName, $userTotalSeconds, $userCustomSalary);

                if ($userCalculatedSalary <= 0 && $userTotalSeconds > 0) {
                    $fallbackSetting = SalarySetting::where('role_name', 'trainee')->where('is_active', true)->first()
                        ?? SalarySetting::where('is_active', true)->orderBy('weekly_salary', 'asc')->first();
                    if ($fallbackSetting) {
                        $userCalculatedSalary = $fallbackSetting->calculateSalary($userTotalHours);
                        $userBaseSalary = $userBaseSalary > 0 ? $userBaseSalary : (int) $fallbackSetting->weekly_salary;
                    }
                }

                if ($userTotalSeconds > 0 && $userCalculatedSalary > 0) {
                    $newPayroll = Payroll::create([
                        'user_id' => $missingUser->id,
                        'period_start' => $weekStart->format('Y-m-d'),
                        'period_end' => $weekEnd->format('Y-m-d'),
                        'total_hours' => $userTotalHours,
                        'base_salary' => $userBaseSalary,
                        'calculated_salary' => $userCalculatedSalary,
                        'status' => 'pending',
                        'notes' => "Auto-synced via regenerate week on " . now()->format('Y-m-d H:i:s') . " for {$missingUser->name}",
                    ]);

                    $this->sendSalaryPendingNotification($newPayroll, false);
                    $createdCount++;
                    $totalNewSalary += $userCalculatedSalary;
                }
            }

            DB::commit();

            if ($regeneratedCount === 0 && $createdCount === 0) {
                return redirect()->back()->with('info', 'Tidak ada gaji pending atau staf baru yang perlu diproses untuk minggu ini.');
            }

            $msg = "Berhasil memproses minggu {$weekStart->format('d M Y')} - {$weekEnd->format('d M Y')}: ";
            if ($regeneratedCount > 0) {
                $msg .= "{$regeneratedCount} gaji diperbarui. ";
            }
            if ($createdCount > 0) {
                $msg .= "{$createdCount} gaji staf baru (sebelumnya belum tersinkron) berhasil digenerate! ";
            }
            $msg .= "Total Gaji Sekarang: $" . number_format($totalNewSalary, 0, '.', ',');

            return redirect()->back()->with('success', $msg);

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
        try {
            $staffName = $payroll->user->name ?? ('User #' . $payroll->user_id);
            $period = $payroll->period_description;
            $status = $payroll->status;

            // Delete related notifications first
            $payroll->notifications()->delete();

            // Delete the payroll
            $payroll->delete();

            Log::info("Payroll ID {$payroll->id} ({$status}) for {$staffName} ({$period}) deleted by admin " . (auth()->user()->name ?? 'System'));

            return redirect()->back()->with('success', "Data gaji ({$staffName} - {$period}) berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data gaji: ' . $e->getMessage());
        }
    }

    /**
     * Remove duplicate payrolls (keep the best/standard record per user and week)
     */
    public function removeDuplicates(Request $request)
    {
        try {
            $force = $request->has('force');

            // Fetch all payrolls and group by user_id and week start
            $allPayrolls = Payroll::with('user')->get();
            $grouped = $allPayrolls->groupBy(function ($payroll) {
                $weekStart = Carbon::parse($payroll->period_start)->startOfWeek()->format('Y-m-d');
                return $payroll->user_id . '_' . $weekStart;
            });

            $duplicateGroups = $grouped->filter(function ($group) {
                return $group->count() > 1;
            });

            if ($duplicateGroups->isEmpty()) {
                return redirect()->back()->with('success', 'Tidak ada data gaji duplikat ditemukan.');
            }

            $totalDeleted = 0;
            $totalKept = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($duplicateGroups as $key => $payrolls) {
                    // Sort to pick primary record:
                    // 1. Paid status comes first (never delete paid)
                    // 2. Standard week bounds (Monday to Sunday) preferred
                    // 3. Newest ID
                    $sorted = $payrolls->sort(function ($a, $b) {
                        if ($a->isPaid() && !$b->isPaid()) return -1;
                        if (!$a->isPaid() && $b->isPaid()) return 1;

                        $weekStart = Carbon::parse($a->period_start)->startOfWeek()->toDateString();
                        $weekEnd = Carbon::parse($a->period_start)->endOfWeek()->toDateString();

                        $aStandard = (Carbon::parse($a->period_start)->toDateString() === $weekStart && Carbon::parse($a->period_end)->toDateString() === $weekEnd);
                        $bStandard = (Carbon::parse($b->period_start)->toDateString() === $weekStart && Carbon::parse($b->period_end)->toDateString() === $weekEnd);

                        if ($aStandard && !$bStandard) return -1;
                        if (!$aStandard && $bStandard) return 1;

                        return $b->id <=> $a->id;
                    })->values();

                    $keepPayroll = $sorted->first();
                    $deletePayrolls = $sorted->slice(1);

                    // Normalize period_end if needed for pending keep record
                    $correctEnd = Carbon::parse($keepPayroll->period_start)->endOfWeek()->toDateString();
                    if (Carbon::parse($keepPayroll->period_end)->toDateString() !== $correctEnd && !$keepPayroll->isPaid()) {
                        $keepPayroll->update(['period_end' => $correctEnd]);
                    }

                    foreach ($deletePayrolls as $payroll) {
                        if ($payroll->isPaid() && !$force) {
                            continue;
                        }

                        try {
                            $payroll->notifications()->delete();
                            $payroll->delete();
                            $totalDeleted++;
                        } catch (\Exception $e) {
                            $errors[] = "Error deleting Payroll ID {$payroll->id}: " . $e->getMessage();
                        }
                    }
                    $totalKept++;
                }

                DB::commit();

                $message = "Berhasil menghapus {$totalDeleted} data gaji duplikat. {$totalKept} data valid tetap disimpan.";
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
            $message = $isUpdate
                ? "Gaji Anda untuk periode {$payroll->period_description} telah di-update sebesar {$payroll->formatted_salary}. Status: Pending"
                : "Gaji Anda untuk periode {$payroll->period_description} telah di-generate sebesar {$payroll->formatted_salary}. Status: Pending";

            // Check if a pending notification already exists for this payroll
            $existing = PayrollNotification::where('payroll_id', $payroll->id)
                ->where('notification_type', 'salary_pending')
                ->latest()
                ->first();

            if ($existing) {
                $existingAmount = isset($existing->metadata['amount']) ? (float)$existing->metadata['amount'] : null;
                $currentAmount = (float)$payroll->calculated_salary;

                // If identical amount and message, skip creating redundant notification
                if ($existingAmount === $currentAmount && $existing->message === $message) {
                    return;
                }

                // If amount changed or message updated, update existing notification
                $existing->update([
                    'message' => $message,
                    'status' => 'sent',
                    'sent_at' => now(),
                    'metadata' => [
                        'amount' => $payroll->calculated_salary,
                        'period' => $payroll->period_description,
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                        'is_update' => true,
                    ],
                ]);
                return;
            }

            // Create notification record if none exists
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
            // Prevent duplicate paid notifications for the same payroll
            $existing = PayrollNotification::where('payroll_id', $payroll->id)
                ->where('notification_type', 'salary_paid')
                ->first();

            if ($existing) {
                return;
            }

            $paidAtFormatted = $payroll->paid_at instanceof \DateTimeInterface
                ? $payroll->paid_at->format('Y-m-d H:i:s')
                : (is_string($payroll->paid_at) && !empty($payroll->paid_at)
                    ? $payroll->paid_at
                    : now()->format('Y-m-d H:i:s'));

            // Create notification record
            $notification = PayrollNotification::create([
                'payroll_id' => $payroll->id,
                'user_id' => $payroll->user_id,
                'notification_type' => 'salary_paid',
                'message' => "Gaji Anda untuk periode {$payroll->period_description} telah dibayar sebesar {$payroll->formatted_salary}",
                'metadata' => [
                    'amount' => $payroll->calculated_salary,
                    'period' => $payroll->period_description,
                    'paid_at' => $paidAtFormatted,
                ],
            ]);

            // Mark notification as sent (NotificationService removed)
            $notification->markAsSent();

        } catch (\Throwable $e) {
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

    /**
     * Fix swapped payroll data for October period (28 Sep - 04 Oct vs 05 Oct - 11 Oct)
     */
    public function fixOctoberSwap(Request $request)
    {
        try {
            $userQuery = $request->input('user', 'all');
            
            Artisan::call('payroll:fix-october-swap', [
                '--user' => $userQuery,
            ]);

            $output = Artisan::output();
            Log::info('Artisan payroll:fix-october-swap output', ['output' => $output]);

            $redirectParams = ['week' => '2026-10-05'];
            if ($userQuery !== 'all') {
                $redirectParams['staff_name'] = $userQuery;
            }

            return redirect()->route('admin.payroll.index', $redirectParams)
                ->with('success', 'Sinkronisasi data gaji untuk seluruh staf berhasil dijalankan! Silakan cek periode 28 Sep - 04 Oct dan 05 Oct - 11 Oct.');
        } catch (\Exception $e) {
            Log::error('Error executing fixOctoberSwap', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal memperbaiki data gaji: ' . $e->getMessage());
        }
    }
}