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
        'supporting_document',
        'status',
        'approved_by_pnd',
        'pnd_reviewed_at',
        'pnd_notes',
        'first_approved_by',
        'first_approved_at',
        'first_approval_notes',
        'final_approved_by',
        'final_approved_at',
        'final_approval_notes',
    ];

    protected $casts = [
        'requirements_checklist' => 'array',
        'pnd_reviewed_at'        => 'datetime',
        'first_approved_at'      => 'datetime',
        'final_approved_at'      => 'datetime',
        'credit_score_at_submission' => 'integer',
        'training_days'          => 'integer',
        'duty_hours'             => 'float',
    ];

    private static bool $schemaChecked = false;

    public static function ensureTableAndColumns(): void
    {
        if (static::$schemaChecked) {
            return;
        }
        static::$schemaChecked = true;

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('promotion_applications')) {
                \Illuminate\Support\Facades\Schema::create('promotion_applications', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                    $table->foreignId('period_id')->constrained('promotion_periods')->cascadeOnDelete();
                    $table->unsignedBigInteger('current_role_id')->nullable();
                    $table->unsignedBigInteger('target_role_id')->nullable();
                    $table->integer('credit_score_at_submission')->default(0);
                    $table->integer('training_days')->default(0);
                    $table->float('duty_hours')->default(0);
                    $table->json('requirements_checklist')->nullable();
                    $table->string('case_study_file')->nullable();
                    $table->string('recommendation_letter_1')->nullable();
                    $table->string('recommendation_letter_2')->nullable();
                    $table->string('supporting_document')->nullable();
                    $table->string('status', 30)->default('pending');
                    $table->foreignId('approved_by_pnd')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('pnd_reviewed_at')->nullable();
                    $table->text('pnd_notes')->nullable();

                    $table->foreignId('first_approved_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('first_approved_at')->nullable();
                    $table->text('first_approval_notes')->nullable();

                    $table->foreignId('final_approved_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('final_approved_at')->nullable();
                    $table->text('final_approval_notes')->nullable();

                    $table->timestamps();
                    $table->index(['user_id', 'period_id', 'status']);
                });
            } else {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('promotion_applications', 'supporting_document')) {
                    \Illuminate\Support\Facades\Schema::table('promotion_applications', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->string('supporting_document')->nullable()->after('recommendation_letter_2');
                    });
                }

                $driver = \Illuminate\Support\Facades\DB::getDriverName();
                if ($driver === 'mysql') {
                    try {
                        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `promotion_applications` MODIFY COLUMN `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
                    } catch (\Throwable $te) {}
                }

                if (!\Illuminate\Support\Facades\Schema::hasColumn('promotion_applications', 'first_approved_by')) {
                    \Illuminate\Support\Facades\Schema::table('promotion_applications', function (\Illuminate\Database\Schema\Blueprint $table) {
                        $table->foreignId('first_approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
                        $table->timestamp('first_approved_at')->nullable()->after('first_approved_by');
                        $table->text('first_approval_notes')->nullable()->after('first_approved_at');
                        $table->foreignId('final_approved_by')->nullable()->after('first_approval_notes')->constrained('users')->nullOnDelete();
                        $table->timestamp('final_approved_at')->nullable()->after('final_approved_by');
                        $table->text('final_approval_notes')->nullable()->after('final_approved_at');
                    });
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PromotionApplication ensureTableAndColumns error: ' . $e->getMessage());
        }
    }

    const STATUS_PENDING  = 'pending';
    const STATUS_ACCEPTED = 'accepted';
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

    public function firstApprover()
    {
        return $this->belongsTo(User::class, 'first_approved_by');
    }

    public function finalApprover()
    {
        return $this->belongsTo(User::class, 'final_approved_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'accepted' => 'Pengajuan Diterima (Menunggu ACC Jabatan)',
            'approved' => 'Kenaikan Jabatan Disahkan',
            'rejected' => 'Ditolak PND',
            default    => 'Menunggu Tinjauan Berkas',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'accepted' => 'blue',
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
