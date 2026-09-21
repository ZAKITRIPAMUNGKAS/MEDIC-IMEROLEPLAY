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

    protected static bool $schemaChecked = false;

    protected static function booted()
    {
        static::ensureSchema();
    }

    public static function ensureSchema(): void
    {
        if (static::$schemaChecked) {
            return;
        }
        static::$schemaChecked = true;

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('candidate_interviews')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
                    \Illuminate\Support\Facades\Schema::table('candidate_interviews', function ($table) {
                        $table->unsignedBigInteger('recruitment_application_id')->nullable()->after('user_id');
                    });
                }
                try {
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE candidate_interviews MODIFY user_id BIGINT UNSIGNED NULL');
                } catch (\Throwable $e) {}
            }
        } catch (\Throwable $e) {}
    }

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
