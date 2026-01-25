<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class SalaryReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'period_start',
        'period_end',
        'total_amount',
        'payroll_count',
        'is_reimbursed',
        'reimbursed_by',
        'reimbursed_at',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'reimbursed_at' => 'datetime',
        'total_amount' => 'integer',
        'payroll_count' => 'integer',
        'is_reimbursed' => 'boolean',
    ];

    /**
     * Get the manager who paid the salaries.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the admin who reimbursed.
     */
    public function reimbursedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reimbursed_by');
    }

    /**
     * Scope for pending reimbursements.
     */
    public function scopePending($query)
    {
        return $query->where('is_reimbursed', false);
    }

    /**
     * Scope for reimbursed records.
     */
    public function scopeReimbursed($query)
    {
        return $query->where('is_reimbursed', true);
    }

    /**
     * Scope for reimbursements in a specific period.
     */
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedAmountAttribute()
    {
        return \App\Helpers\PayrollHelper::formatCurrency($this->total_amount);
    }

    /**
     * Get period description.
     */
    public function getPeriodDescriptionAttribute()
    {
        $start = Carbon::parse($this->period_start)->format('d M Y');
        $end = Carbon::parse($this->period_end)->format('d M Y');
        return "{$start} - {$end}";
    }

    /**
     * Check if reimbursement is pending.
     */
    public function isPending(): bool
    {
        return !$this->is_reimbursed;
    }

    /**
     * Check if already reimbursed.
     */
    public function isReimbursed(): bool
    {
        return $this->is_reimbursed;
    }

    /**
     * Mark as reimbursed.
     */
    public function markAsReimbursed($reimbursedBy = null, $notes = null): bool
    {
        return $this->update([
            'is_reimbursed' => true,
            'reimbursed_at' => now(),
            'reimbursed_by' => $reimbursedBy ?? auth()->id(),
            'notes' => $notes ?? $this->notes,
        ]);
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColorAttribute()
    {
        return $this->is_reimbursed ? 'success' : 'warning';
    }

    /**
     * Get status text.
     */
    public function getStatusTextAttribute()
    {
        return $this->is_reimbursed ? 'Sudah Direimburse' : 'Belum Direimburse';
    }
}
