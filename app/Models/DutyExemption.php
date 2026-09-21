<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyExemption extends Model
{
    protected $fillable = [
        'user_id',
        'month_period',
        'period',
        'leave_request_id',
        'exempted_by',
        'reason',
    ];

    public static function getPeriodColumn(): string
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('duty_exemptions', 'month_period')) {
                return 'month_period';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('duty_exemptions', 'period')) {
                return 'period';
            }
        } catch (\Throwable $e) {
            // fallback
        }
        return 'month_period';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function exemptedBy()
    {
        return $this->belongsTo(User::class, 'exempted_by');
    }
}
