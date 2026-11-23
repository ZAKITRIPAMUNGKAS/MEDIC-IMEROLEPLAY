<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_name',
        'poli',
        'day',
        'start_time',
        'end_time',
        'hospital',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'day' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Dapatkan nama spesialisasi/poli tanpa emoji di depan
     */
    public function cleanPoliName(): string
    {
        return trim(preg_replace('/^[\x{1F300}-\x{1FAD6}\x{2600}-\x{27BF}\x{1F600}-\x{1F64F}\s]+/u', '', $this->poli ?? ''));
    }
}
