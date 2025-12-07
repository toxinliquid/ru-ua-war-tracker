<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarPostItem extends Model
{
    protected $fillable = [
        'war_post_id',
        'image_path',
        'image_alt',
        'side',
        'short_description',
        'long_description',
        'advance_km2',
        'position',
    ];

    protected $casts = [
        'advance_km2' => 'decimal:2',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(WarPost::class, 'war_post_id');
    }
}
