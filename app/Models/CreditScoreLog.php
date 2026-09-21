<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditScoreLog extends Model
{
    protected $fillable = [
        'user_id',
        'issued_by',
        'amount',
        'balance_after',
        'reason',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
