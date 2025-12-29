<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'total_hours',
        'base_salary',
        'calculated_salary',
        'status',
        'paid_at',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'base_salary' => 'integer',
        'calculated_salary' => 'integer',
    ];

    /**
     * Get the user that owns the payroll.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who paid the payroll.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the payroll notifications.
     */
    public function notifications()
    {
        return $this->hasMany(PayrollNotification::class);
    }

    /**
     * Scope for pending payrolls.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid payrolls.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for cancelled payrolls.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for payrolls in a specific period.
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    /**
     * Get formatted salary amount.
     */
    public function getFormattedSalaryAttribute()
    {
        try {
            if (!$this->calculated_salary || $this->calculated_salary <= 0) {
                return '$ 0';
            }
            return \App\Helpers\PayrollHelper::formatCurrency($this->calculated_salary);
        } catch (\Exception $e) {
            \Log::warning('Error formatting salary', [
                'payroll_id' => $this->id,
                'calculated_salary' => $this->calculated_salary,
                'error' => $e->getMessage()
            ]);
            return '$ 0';
        }
    }

    /**
     * Get formatted base salary amount.
     */
    public function getFormattedBaseSalaryAttribute()
    {
        return \App\Helpers\PayrollHelper::formatCurrency($this->base_salary);
    }

    /**
     * Get formatted total hours.
     */
    public function getFormattedHoursAttribute()
    {
        try {
            // total_hours is now stored as decimal hours, convert to seconds for formatDuration
            if (!$this->total_hours || $this->total_hours <= 0) {
                return '00:00:00';
            }
            $totalSeconds = (int) ($this->total_hours * 3600);
            return \App\Helpers\TimeHelper::formatDuration($totalSeconds);
        } catch (\Exception $e) {
            \Log::warning('Error formatting hours', [
                'payroll_id' => $this->id,
                'total_hours' => $this->total_hours,
                'error' => $e->getMessage()
            ]);
            return '00:00:00';
        }
    }

    /**
     * Get period description.
     */
    public function getPeriodDescriptionAttribute()
    {
        try {
            if (!$this->period_start || !$this->period_end) {
                return '-';
            }
            $start = Carbon::parse($this->period_start)->format('d M Y');
            $end = Carbon::parse($this->period_end)->format('d M Y');
            return "{$start} - {$end}";
        } catch (\Exception $e) {
            \Log::warning('Error formatting period description', [
                'payroll_id' => $this->id,
                'period_start' => $this->period_start,
                'period_end' => $this->period_end,
                'error' => $e->getMessage()
            ]);
            return '-';
        }
    }

    /**
     * Check if payroll is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if payroll is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payroll is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Mark payroll as paid.
     */
    public function markAsPaid($paidBy = null): bool
    {
        return $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $paidBy,
        ]);
    }

    /**
     * Mark payroll as cancelled.
     */
    public function markAsCancelled(): bool
    {
        return $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Resolve route model binding with user constraint for staff
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // If we're in staff context (checking route name, path, or middleware)
        $isStaffRoute = false;
        
        // Check multiple ways to determine if this is a staff route
        if (request()->routeIs('staff.*')) {
            $isStaffRoute = true;
        } elseif (request()->is('staff/*')) {
            $isStaffRoute = true;
        } elseif (request()->route() && strpos(request()->route()->getName() ?? '', 'staff.') === 0) {
            $isStaffRoute = true;
        }
        
        if ($isStaffRoute && auth()->check()) {
            $payroll = $this->where('id', $value)
                ->where('user_id', auth()->id())
                ->first();
            
            if (!$payroll) {
                abort(404, 'Gaji tidak ditemukan atau Anda tidak memiliki akses.');
            }
            
            return $payroll;
        }
        
        // Default behavior for admin or other contexts
        return parent::resolveRouteBinding($value, $field);
    }
}
