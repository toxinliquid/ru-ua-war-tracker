<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WestNews extends Model
{
    use HasFactory;

    public const LINK_TYPES = [
        'humiliated' => 'Humiliated',
        'spinning'   => 'Spinning',
        'reeling'    => 'Reeling',
        'sick'       => 'Sick',
        'dead'       => 'Dead',
        'weaponized' => 'Weaponized',
        'russia_collapsing' => 'Russia is Collapsing',
        'russia_invade_europe' => 'Russia will Invade Europe',
    ];

    protected $fillable = [
        'title',
        'link_type',
        'url',
    ];

    protected $casts = [
        'title'     => 'string',
        'link_type' => 'string',
        'url'       => 'string',
    ];

    public function getLinkTypeLabelAttribute(): string
    {
        return self::LINK_TYPES[$this->link_type] ?? ucfirst($this->link_type);
    }
}
