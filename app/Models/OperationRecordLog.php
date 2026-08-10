<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationRecordLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_record_id',
        'user_id',
        'action',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function operationRecord()
    {
        return $this->belongsTo(OperationRecord::class, 'operation_record_id');
    }
}
