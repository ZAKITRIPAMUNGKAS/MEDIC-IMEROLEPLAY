<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'name',
        'file_url',
        'file_type',
        'keywords',
        'is_animated',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'pack_id' => 'integer',
        'is_animated' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pack(): BelongsTo
    {
        return $this->belongsTo(StickerPack::class, 'pack_id');
    }
}
