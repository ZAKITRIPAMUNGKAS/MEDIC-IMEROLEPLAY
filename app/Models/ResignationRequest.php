<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payroll;
use App\Helpers\PayrollHelper;
use Illuminate\Support\Facades\Storage;

class ResignationRequest extends Model
{
    // Tipe Pengajuan / Sanksi
    const TYPE_RESIGNATION = 'resignation';
    const TYPE_PTDH        = 'ptdh';

    protected $fillable = [
        'user_id',
        'type',
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
        'ptdh_additional_fee',
        'fine_amount',
        'fine_paid',
        'ie_verified_by',
        'ie_verified_at',
        'ie_notes',
        // Bukti-bukti resign
        'pocket_proof',
        'key_proof',
        'letter_proof',
        'fine_proof',
        'proof_submitted_at',
        'proof_revision_notes',
        // Penonaktifan akhir
        'final_deactivated_by',
        'final_deactivated_at',
    ];

    protected $casts = [
        'letter_date'          => 'date',
        'pnd_approved_at'      => 'datetime',
        'ie_verified_at'       => 'datetime',
        'proof_submitted_at'   => 'datetime',
        'final_deactivated_at' => 'datetime',
        'base_salary'          => 'integer',
        'fine_percentage'      => 'float',
        'ptdh_additional_fee'  => 'integer',
        'fine_amount'          => 'integer',
        'fine_paid'            => 'boolean',
    ];

    // Status constants
    const STATUS_PENDING_PND     = 'pending_pnd';
    const STATUS_APPROVED_PND    = 'approved_pnd';
    const STATUS_PENDING_IE      = 'pending_ie';
    const STATUS_PENDING_PROOF   = 'pending_proof';
    const STATUS_PROOF_SUBMITTED = 'proof_submitted';
    const STATUS_PROOF_REVISION  = 'proof_revision';
    const STATUS_COMPLETED       = 'completed';
    const STATUS_REJECTED        = 'rejected';
    const STATUS_CANCELLED       = 'cancelled';

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

    public function finalDeactivatedBy()
    {
        return $this->belongsTo(User::class, 'final_deactivated_by');
    }

    public function resignationLog()
    {
        return $this->hasOne(ResignationLog::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING_PND     => 'Menunggu Verifikasi PND',
            self::STATUS_APPROVED_PND    => 'Disetujui PND, Menunggu IE',
            self::STATUS_PENDING_IE      => 'Kalkulasi Denda oleh IE',
            self::STATUS_PENDING_PROOF   => 'Menunggu Upload Bukti Resign',
            self::STATUS_PROOF_SUBMITTED => 'Bukti Telah Diunggah (Verifikasi IE)',
            self::STATUS_PROOF_REVISION  => 'Revisi Bukti Diminta IE',
            self::STATUS_COMPLETED       => 'Selesai (Not Active)',
            self::STATUS_REJECTED        => 'Ditolak',
            self::STATUS_CANCELLED       => 'Dibatalkan',
            default                      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED       => 'emerald',
            self::STATUS_REJECTED        => 'red',
            self::STATUS_CANCELLED       => 'gray',
            self::STATUS_PENDING_PND     => 'yellow',
            self::STATUS_PENDING_IE      => 'orange',
            self::STATUS_PENDING_PROOF   => 'purple',
            self::STATUS_PROOF_SUBMITTED => 'sky',
            self::STATUS_PROOF_REVISION  => 'rose',
            default                      => 'blue',
        };
    }

    // ─── Bukti URL Accessors ──────────────────────────────────────────────────

    public function getPocketProofUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->pocket_proof);
    }

    public function getKeyProofUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->key_proof);
    }

    public function getLetterProofUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->letter_proof);
    }

    public function getFineProofUrlAttribute(): ?string
    {
        return $this->resolveFileUrl($this->fine_proof);
    }

    private function resolveFileUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Apakah anggota saat ini berada pada tahap wajib mengunggah/mengisi ulang bukti?
     */
    public function canUploadProof(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING_PROOF, self::STATUS_PROOF_REVISION]);
    }

    /**
     * Apakah seluruh 4 bukti resign telah terunggah lengkap?
     */
    public function hasAllProofs(): bool
    {
        return !empty($this->pocket_proof)
            && !empty($this->key_proof)
            && !empty($this->letter_proof)
            && !empty($this->fine_proof);
    }

    /**
     * Hitung denda otomatis berdasarkan jabatan dan total akumulasi gaji pokok yang diterima.
     */
    public function calculateFine(): void
    {
        $user = $this->user ?? User::find($this->user_id);

        if ($user) {
            // Hitung akumulasi gaji pokok dari seluruh riwayat payroll yang telah dibayar (status = 'paid')
            $paidBaseSalarySum = Payroll::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('base_salary');

            if ($paidBaseSalarySum > 0) {
                $this->base_salary = (int) $paidBaseSalarySum;
            } else {
                // Fallback jika belum pernah ada payroll berstatus paid
                $allBaseSalarySum = Payroll::where('user_id', $user->id)->sum('base_salary');
                if ($allBaseSalarySum > 0) {
                    $this->base_salary = (int) $allBaseSalarySum;
                } elseif (!$this->base_salary || $this->base_salary <= 0) {
                    $this->base_salary = (int) PayrollHelper::getBaseSalary($user->role?->name, $user->custom_salary ?? 0);
                }
            }
        }

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
        $baseFine              = (int) round(($this->base_salary ?? 0) * $pct / 100);
        $additionalFee         = $this->isPtdh() ? (int) ($this->ptdh_additional_fee ?? 250000) : 0;
        $this->fine_amount     = $baseFine + $additionalFee;
    }

    /**
     * Apakah pengajuan ini merupakan sanksi PTDH?
     */
    public function isPtdh(): bool
    {
        return ($this->type ?? self::TYPE_RESIGNATION) === self::TYPE_PTDH;
    }

    /**
     * Denda murni sebelum biaya tambahan PTDH.
     */
    public function getBaseFineAmountAttribute(): int
    {
        return (int) round(($this->base_salary ?? 0) * ($this->fine_percentage ?? 30) / 100);
    }

    /**
     * Apakah sudah di tahap IE (Denda atau Verifikasi Bukti)?
     */
    public function isAtIeStage(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_IE,
            self::STATUS_APPROVED_PND,
            self::STATUS_PENDING_PROOF,
            self::STATUS_PROOF_SUBMITTED,
            self::STATUS_PROOF_REVISION,
        ]);
    }
}
