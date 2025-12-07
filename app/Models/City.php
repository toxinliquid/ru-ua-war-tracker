<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'russian_control_percent',
        'ukrainian_control_percent',
    ];

    protected $casts = [
        'russian_control_percent'   => 'float',
        'ukrainian_control_percent' => 'float',
    ];

    public function getNeutralControlPercentAttribute(): float
    {
        $ru = max(0.0, min(100.0, $this->russian_control_percent));
        $ua = max(0.0, min(100.0, $this->ukrainian_control_percent));

        $remainder = max(0.0, 100.0 - ($ru + $ua));

        return round($remainder, 2);
    }
}
