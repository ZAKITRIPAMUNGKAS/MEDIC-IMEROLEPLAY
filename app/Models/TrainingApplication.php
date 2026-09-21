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

    const TYPE_OPERASI        = 'operasi';
    const TYPE_SURAT_MENYURAT = 'surat_menyurat';
    const TYPE_VISUM_HIDUP    = 'visum_hidup';

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_OPERASI        => 'Pelatihan Operasi',
            self::TYPE_SURAT_MENYURAT => 'Pelatihan Surat Menyurat',
            self::TYPE_VISUM_HIDUP    => 'Pelatihan Visum Hidup',
        ];
    }

    public static function typeFullTitles(): array
    {
        return [
            self::TYPE_OPERASI        => 'FORMULIR PENDAFTARAN PELATIHAN OPERASI FASE XIII',
            self::TYPE_SURAT_MENYURAT => 'Formulir Pendaftaran Surat Menyurat',
            self::TYPE_VISUM_HIDUP    => 'PENDAFTARAN PELATIHAN VISUM HIDUP',
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
            self::STATUS_APPROVED => '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200"><i class="fas fa-check-circle text-[11px]"></i> Disetujui</span>',
            self::STATUS_REJECTED => '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200"><i class="fas fa-times-circle text-[11px]"></i> Ditolak</span>',
            default               => '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200"><i class="fas fa-clock text-[11px]"></i> Menunggu Review</span>',
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
