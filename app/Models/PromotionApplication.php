<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionApplication extends Model
{
    protected $fillable = [
        'user_id',
        'period_id',
        'current_role_id',
        'target_role_id',
        'credit_score_at_submission',
        'training_days',
        'duty_hours',
        'requirements_checklist', // JSON snapshot of met/unmet requirements
        'case_study_file',
        'recommendation_letter_1',
        'recommendation_letter_2',
        'status',
        'approved_by_pnd',
        'pnd_reviewed_at',
        'pnd_notes',
    ];

    protected $casts = [
        'requirements_checklist' => 'array',
        'pnd_reviewed_at'        => 'datetime',
        'credit_score_at_submission' => 'integer',
        'training_days'          => 'integer',
        'duty_hours'             => 'float',
    ];

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function period()
    {
        return $this->belongsTo(PromotionPeriod::class, 'period_id');
    }

    public function currentRole()
    {
        return $this->belongsTo(StaffRole::class, 'current_role_id');
    }

    public function targetRole()
    {
        return $this->belongsTo(StaffRole::class, 'target_role_id');
    }

    public function approvedByPnd()
    {
        return $this->belongsTo(User::class, 'approved_by_pnd');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui PND',
            'rejected' => 'Ditolak PND',
            default    => 'Menunggu Tinjauan PND',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'yellow',
        };
    }

    /**
     * Cek apakah semua requirement telah terpenuhi.
     */
    public function allRequirementsMet(): bool
    {
        if (empty($this->requirements_checklist)) return false;
        return collect($this->requirements_checklist)->every(fn($r) => $r['met'] === true);
    }
}
