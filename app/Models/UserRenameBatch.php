<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRenameBatch extends Model
{
    protected $fillable = [
        'batch_name',
        'description',
        'total_users',
        'successful_renames',
        'failed_renames',
        'mapping_data',
        'rename_log',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'mapping_data' => 'array',
        'rename_log' => 'array',
        'processed_at' => 'datetime',
    ];

    /**
     * Get all rename logs for this batch
     */
    public function logs(): HasMany
    {
        return $this->hasMany(UserRenameLog::class, 'batch_id');
    }
}
