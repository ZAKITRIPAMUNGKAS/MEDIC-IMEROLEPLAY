<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateApplication extends Model
{
    protected $fillable = [
        'user_id',
        'type',             // vehicle_land, vehicle_heli, operation_cert
        'division',         // ga, pnd
        'title',
        'reason',
        'notes',
        'status',           // pending, approved, rejected
        'verified_by',
        'verified_at',
        'admin_notes',
        'certification_id',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    private static bool $schemaChecked = false;

    public static function ensureTableExists(): void
    {
        if (static::$schemaChecked) {
            return;
        }
        static::$schemaChecked = true;

        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('certificate_applications')) {
                \Illuminate\Support\Facades\Schema::create('certificate_applications', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                    $table->string('type'); // 'vehicle_land', 'vehicle_heli', 'operation_cert'
                    $table->string('division'); // 'ga', 'pnd'
                    $table->string('title');
                    $table->text('reason')->nullable();
                    $table->text('notes')->nullable();
                    $table->string('status', 30)->default('pending'); // 'pending', 'approved', 'rejected'
                    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                    $table->timestamp('verified_at')->nullable();
                    $table->text('admin_notes')->nullable();
                    $table->foreignId('certification_id')->nullable()->constrained('member_certifications')->nullOnDelete();
                    $table->timestamps();

                    $table->index(['user_id', 'division', 'status']);
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CertificateApplication ensureTableExists error: ' . $e->getMessage());
        }
    }

    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function certification()
    {
        return $this->belongsTo(MemberCertification::class, 'certification_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui & Diterbitkan',
            'rejected' => 'Ditolak',
            default    => 'Menunggu Peninjauan',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'emerald',
            'rejected' => 'rose',
            default    => 'amber',
        };
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'vehicle_land'   => 'Kendaraan Darat (Ambulans)',
            'vehicle_heli'   => 'Helikopter Medis',
            'operation_cert' => 'Sertifikasi Operasi Medis',
            default          => $this->title ?? 'Sertifikat',
        };
    }
}
