<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyExemption extends Model
{
    protected $fillable = [
        'user_id',
        'month_period',
        'leave_request_id',
        'exempted_by',
        'reason',
    ];

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
