<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
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
}
