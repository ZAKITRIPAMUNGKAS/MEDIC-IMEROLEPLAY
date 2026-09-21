<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateInterview extends Model
{
    protected $fillable = [
        'user_id',
        'recruitment_application_id',
        'interviewer_id',
        'result',
        'recommended_role',
        'notes',
        'interviewed_at',
    ];

    protected $casts = [
        'interviewed_at' => 'datetime',
    ];

    public function candidate()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function application()
    {
        return $this->belongsTo(RecruitmentApplication::class, 'recruitment_application_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getRecommendedRoleLabelAttribute(): string
    {
        return match($this->recommended_role) {
            'trainee' => 'Trainee',
            'perawat' => 'Perawat',
            'co_ass' => 'Co-Ass',
            'dokter_umum' => 'Dokter Umum',
            default => $this->recommended_role ?? '—',
        };
    }
}
