<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarPost extends Model
{
    protected $fillable = [
        'title',
        'date_from',
        'date_to',
        'day_from',
        'day_to',
        'description',
        'total_russian_gross_km2',
        'total_ukrainian_gross_km2',
        'overall_ukrainian_advance_km2',
    ];

    protected $casts = [
        'date_from'                     => 'date',
        'date_to'                       => 'date',
        'overall_ukrainian_advance_km2' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(WarPostItem::class)->orderBy('position');
    }

    // Compute totals live (used if you don’t trust cached)
    public function computedTotals(): array
    {
        $ru = (float) $this->items()
            ->where('side', 'russia')
            ->sum('advance_km2');

        $ua = (float) $this->items()
            ->where('side', 'ukraine')
            ->sum('advance_km2');

        return [
            'russia'  => round($ru, 2),
            'ukraine' => round($ua, 2),
        ];
    }

    // Keep cache in sync
    public function refreshCachedTotals(): void
    {
        $totals = $this->computedTotals();

        $this->forceFill([
            'total_russian_gross_km2'  => $totals['russia'],
            'total_ukrainian_gross_km2'=> $totals['ukraine'],
        ])->save();
    }
}
