<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VisitStatsController extends Controller
{
    public function __invoke(): View
    {
        $countries = Visit::select(
                'country',
                DB::raw('COUNT(*) as hits'),
                DB::raw('COUNT(DISTINCT ip) as unique_ips')
            )
            ->groupBy('country')
            ->orderByDesc('unique_ips')
            ->get();

        $totals = [
            'hits'   => Visit::count(),
            'unique' => Visit::distinct('ip')->count('ip'),
        ];

        return view('admin.visits.index', [
            'countries' => $countries,
            'totals'    => $totals,
        ]);
    }
}
