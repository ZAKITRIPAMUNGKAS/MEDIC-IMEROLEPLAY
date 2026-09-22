<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ResignationLog extends Model
{
    use HasFactory;

    protected $table = 'resignation_logs';

    // Tipe Pengajuan / Sanksi
    const TYPE_RESIGNATION = 'resignation';
    const TYPE_PTDH        = 'ptdh';

    protected $fillable = [
        'resignation_request_id',
        'user_id',
        'type',
        'member_name',
        'citizen_id',
        'last_position',
        'managerial_position',
        'batch',
        'hospital',
        'resignation_date',
        'deactivated_at',
        'reason',
        'reason_ic',
        'reason_ooc',
        'total_fine',
        'fine_percentage',
        'ptdh_additional_fee',
        'fine_status',
        'pocket_proof',
        'key_proof',
        'letter_proof',
        'fine_proof',
        'ie_verifier_name',
        'ie_deactivator_name',
        'notes',
    ];

    protected $casts = [
        'resignation_date'    => 'date',
        'deactivated_at'      => 'datetime',
        'total_fine'          => 'integer',
        'fine_percentage'     => 'float',
        'ptdh_additional_fee' => 'integer',
    ];

    public function isPtdh(): bool
    {
        return ($this->type ?? self::TYPE_RESIGNATION) === self::TYPE_PTDH;
    }

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resignationRequest()
    {
        return $this->belongsTo(ResignationRequest::class);
    }

    // ─── Helpers URL Bukti ───────────────────────────────────────────────────

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
}
