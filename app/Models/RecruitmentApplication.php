<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentApplication extends Model
{
    protected $fillable = [
        'period_id',
        'hospital',
        'agree_general_req',
        'agree_special_req',
        'ic_name',
        'cid',
        'gender',
        'birth_date',
        'has_medical_exp',
        'medical_exp_desc',
        'reason_joining',
        'rp_experience',
        'ktp_file',
        'skb_file',
        'health_cert_file',
        'psychology_cert_file',
        'other_city_responsibility',
        'online_hours',
        'online_days',
        'discord_username',
        'email',
        'password_temp',
        'status',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
        'user_id',
    ];

    protected $casts = [
        'agree_general_req' => 'boolean',
        'agree_special_req' => 'boolean',
        'birth_date'        => 'date',
        'online_hours'      => 'array',
        'online_days'       => 'array',
        'reviewed_at'       => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function period()
    {
        return $this->belongsTo(RecruitmentPeriod::class, 'period_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function candidateInterviews()
    {
        return $this->hasMany(CandidateInterview::class, 'recruitment_application_id');
    }

    public function latestInterview()
    {
        return $this->hasOne(CandidateInterview::class, 'recruitment_application_id')->latestOfMany();
    }

    // ─── Accessors / Helpers ──────────────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Review</span>',
            'reviewed'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Lolos Berkas</span>',
            'interview' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">Tahap Wawancara</span>',
            'accepted'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">Diterima</span>',
            'rejected'  => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">Ditolak</span>',
            default     => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">' . e($this->status) . '</span>',
        };
    }
}
