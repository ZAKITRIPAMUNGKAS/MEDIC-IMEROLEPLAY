<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResignationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'letter_date',
        'applicant_name',
        'position',
        'managerial_position',
        'batch',
        'reason_ic',
        'reason_ooc',
        'standard_text',
        // Approval flow
        'status',
        'pnd_approved_by',
        'pnd_approved_at',
        'pnd_notes',
        // IE denda
        'base_salary',
        'fine_percentage',
        'fine_amount',
        'fine_paid',
        'ie_verified_by',
        'ie_verified_at',
        'ie_notes',
    ];

    protected $casts = [
        'letter_date'    => 'date',
        'pnd_approved_at'=> 'datetime',
        'ie_verified_at' => 'datetime',
        'base_salary'    => 'integer',
        'fine_percentage'=> 'float',
        'fine_amount'    => 'integer',
        'fine_paid'      => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING_PND   = 'pending_pnd';
    const STATUS_APPROVED_PND  = 'approved_pnd';
    const STATUS_PENDING_IE    = 'pending_ie';
    const STATUS_COMPLETED     = 'completed';
    const STATUS_REJECTED      = 'rejected';
    const STATUS_CANCELLED     = 'cancelled';

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pndApprovedBy()
    {
        return $this->belongsTo(User::class, 'pnd_approved_by');
    }

    public function ieVerifiedBy()
    {
        return $this->belongsTo(User::class, 'ie_verified_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending_pnd'  => 'Menunggu Verifikasi PND',
            'approved_pnd' => 'Disetujui PND, Menunggu IE',
            'pending_ie'   => 'Kalkulasi Denda oleh IE',
            'completed'    => 'Selesai (Akun Dinonaktifkan)',
            'rejected'     => 'Ditolak',
            'cancelled'    => 'Dibatalkan',
            default        => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed'   => 'green',
            'rejected'    => 'red',
            'cancelled'   => 'gray',
            'pending_pnd' => 'yellow',
            default       => 'blue',
        };
    }

    /**
     * Hitung denda otomatis berdasarkan jabatan.
     * Perawat – Co-Ass: 30% dari Total Gaji Pokok
     * Dokter Umum: 25% dari Total Gaji Pokok
     */
    public function calculateFine(): void
    {
        // Prioritaskan cek jabatan medis klinis jika ada
        $userMedic = $this->user?->effective_medic_role?->name ?? $this->user?->role?->name ?? '';
        $posName = strtolower(trim(str_replace([' ', '-'], '_', (string) ($userMedic ?: $this->position))));

        if (str_contains($posName, 'dokter_umum') || str_contains($posName, 'dokter umum')) {
            $pct = 25.0;
        } elseif (str_contains($posName, 'perawat') || str_contains($posName, 'co_ass') || str_contains($posName, 'coass') || str_contains($posName, 'trainee')) {
            $pct = 30.0;
        } else {
            $pct = 30.0;
        }

        $this->fine_percentage = $pct;
        $this->fine_amount     = (int) round($this->base_salary * $pct / 100);
    }

    /**
     * Apakah sudah di tahap IE (Denda)?
     */
    public function isAtIeStage(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING_IE, self::STATUS_APPROVED_PND]);
    }
}
