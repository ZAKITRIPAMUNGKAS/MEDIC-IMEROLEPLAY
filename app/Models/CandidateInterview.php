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
        'recommendation',
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
                // 1. recruitment_application_id
                if (!\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recruitment_application_id')) {
                    \Illuminate\Support\Facades\Schema::table('candidate_interviews', function ($table) {
                        $table->unsignedBigInteger('recruitment_application_id')->nullable();
                    });
                }

                // 2. result
                if (!\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'result')) {
                    \Illuminate\Support\Facades\Schema::table('candidate_interviews', function ($table) {
                        $table->string('result', 30)->default('recommended')->after('interviewer_id');
                    });
                    if (\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recommendation')) {
                        \Illuminate\Support\Facades\DB::statement("UPDATE candidate_interviews SET result = recommendation WHERE result IS NULL OR result = ''");
                    }
                }

                // 3. recommendation (nullable for legacy DB)
                if (\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recommendation')) {
                    try {
                        \Illuminate\Support\Facades\DB::statement('ALTER TABLE candidate_interviews MODIFY recommendation VARCHAR(30) NULL');
                    } catch (\Throwable $e) {}
                }

                // 4. interviewed_at
                if (!\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'interviewed_at')) {
                    \Illuminate\Support\Facades\Schema::table('candidate_interviews', function ($table) {
                        $table->timestamp('interviewed_at')->nullable()->after('notes');
                    });
                }

                // 5. recommended_role
                if (!\Illuminate\Support\Facades\Schema::hasColumn('candidate_interviews', 'recommended_role')) {
                    \Illuminate\Support\Facades\Schema::table('candidate_interviews', function ($table) {
                        $table->string('recommended_role', 50)->nullable()->after('result');
                    });
                }

                // 6. user_id nullable (disable FK checks during alter)
                try {
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                    \Illuminate\Support\Facades\DB::statement('ALTER TABLE candidate_interviews MODIFY user_id BIGINT UNSIGNED NULL');
                    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1');
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
