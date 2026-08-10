<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_record_id',
        'file_path'
    ];

    public function operationRecord()
    {
        return $this->belongsTo(OperationRecord::class, 'operation_record_id');
    }
}
