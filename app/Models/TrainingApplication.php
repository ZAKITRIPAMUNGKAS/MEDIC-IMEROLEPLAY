<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingApplication extends Model
{
    use HasFactory;

    protected $table = 'training_applications';

    protected $fillable = [
        'user_id',
        'training_type',
        'nama_ic',
        'gender',
        'phone_ic',
        'jabatan',
        'batch',
        'additional_data',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'reviewed_at'     => 'datetime',
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
            if (!\Illuminate\Support\Facades\Schema::hasTable('training_applications')) {
                \Illuminate\Support\Facades\Schema::create('training_applications', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                    $table->string('training_type'); // 'operasi', 'surat_menyurat', 'visum_hidup'
                    $table->string('nama_ic');
                    $table->string('gender'); // 'Laki-laki', 'Perempuan'
                    $table->string('phone_ic')->nullable();
                    $table->string('jabatan')->nullable();
                    $table->string('batch');
                    $table->json('additional_data')->nullable();
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                    $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('reviewed_at')->nullable();
                    $table->text('admin_notes')->nullable();
                    $table->timestamps();

                    $table->index(['training_type', 'status']);
                    $table->index(['training_type', 'batch']);
                    $table->index('user_id');
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore or log
        }
    }

    const TYPE_OPERASI           = 'operasi';
    const TYPE_SURAT_MENYURAT    = 'surat_menyurat';
    const TYPE_VISUM_HIDUP       = 'visum_hidup';
    const TYPE_REKAM_MEDIS       = 'rekam_medis';
    const TYPE_PEMULSARAN_JENAZAH = 'pemulsaran_jenazah';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_OPERASI            => 'Pelatihan Operasi',
            self::TYPE_SURAT_MENYURAT     => 'Pelatihan Surat Menyurat',
            self::TYPE_VISUM_HIDUP        => 'Pelatihan Visum Hidup',
            self::TYPE_REKAM_MEDIS        => 'Pelatihan Rekam Medis',
            self::TYPE_PEMULSARAN_JENAZAH => 'Pelatihan Pemulsaran Jenazah',
        ];
    }

    public static function typeFullTitles(): array
    {
        return [
            self::TYPE_OPERASI            => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII',
            self::TYPE_SURAT_MENYURAT     => 'Formulir Pendaftaran Surat Menyurat',
            self::TYPE_VISUM_HIDUP        => 'PENDAFTARAN PELATIHAN VISUM HIDUP',
            self::TYPE_REKAM_MEDIS        => 'PENDAFTARAN PELATIHAN REKAM MEDIS',
            self::TYPE_PEMULSARAN_JENAZAH => 'PENDAFTARAN PELATIHAN PEMULSARAN JENAZAH',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->training_type] ?? ucfirst(str_replace('_', ' ', $this->training_type));
    }

    public function getFullTitleAttribute(): string
    {
        return self::typeFullTitles()[$this->training_type] ?? $this->type_label;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm whitespace-nowrap"><i class="fas fa-check-circle text-[11px] text-emerald-400"></i> Disetujui</span>',
            self::STATUS_REJECTED => '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm whitespace-nowrap"><i class="fas fa-times-circle text-[11px] text-rose-400"></i> Ditolak</span>',
            default               => '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm whitespace-nowrap"><i class="fas fa-clock text-[11px] text-amber-400"></i> Menunggu Review</span>',
        };
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
