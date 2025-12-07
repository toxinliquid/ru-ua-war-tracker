<?php

namespace App\Http\Controllers;

use App\Models\WarPost;
use Illuminate\View\View;

class WarPostShowController extends Controller
{
    /**
     * Display a public war post page.
     */
    public function __invoke(WarPost $warPost): View
    {
        $warPost->load(['items' => fn ($query) => $query->orderBy('position')]);

        $totals = [
            'russia'  => round((float) $warPost->items->where('side', 'russia')->sum('advance_km2'), 2),
            'ukraine' => round((float) $warPost->items->where('side', 'ukraine')->sum('advance_km2'), 2),
        ];

        return view('war-posts.show', [
            'post'    => $warPost,
            'totals'  => $totals,
            'net'     => round($totals['ukraine'] - $totals['russia'], 2),
        ]);
    }
}
