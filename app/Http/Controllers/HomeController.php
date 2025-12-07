<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Region;
use App\Models\WarPost;
use App\Models\WestNews;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $latestPosts = WarPost::orderByDesc('date_to')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $cityStats = $this->buildSummary(
            City::orderBy('name')->get(),
            'russian_control_percent',
            'ukrainian_control_percent'
        );

        $regionStats = $this->buildSummary(
            Region::orderBy('name')->get(),
            'russian_control_percent',
            'ukrainian_control_percent'
        );

        $warStats = [
            'total_posts'   => WarPost::count(),
            'total_ru_km2'  => round((float) WarPost::sum('total_russian_gross_km2'), 1),
            'total_ua_km2'  => round((float) WarPost::sum('total_ukrainian_gross_km2'), 1),
            'latest_period' => optional($latestPosts->first()?->date_to)->toFormattedDateString(),
        ];

        $latestArticles = WestNews::orderByDesc('created_at')
            ->take(6)
            ->get();

        return view('welcome', [
            'latestPosts' => $latestPosts,
            'cityStats'   => $cityStats,
            'regionStats' => $regionStats,
            'warStats'    => $warStats,
            'latestArticles' => $latestArticles,
        ]);
    }

    /**
     * @param  Collection<int, mixed>  $collection
     */
    private function buildSummary(Collection $collection, string $ruKey, string $uaKey): array
    {
        $count = $collection->count();

        $avgRu = $count ? round((float) $collection->avg($ruKey), 1) : 0.0;
        $avgUa = $count ? round((float) $collection->avg($uaKey), 1) : 0.0;

        $leadersUa = $collection->sortByDesc($uaKey)->take(3);
        $leadersRu = $collection->sortByDesc($ruKey)->take(3);
        $contested = $collection->sortBy(function ($item) use ($ruKey, $uaKey) {
            return abs((float) $item->{$ruKey} - (float) $item->{$uaKey});
        })->take(3);

        return [
            'count'        => $count,
            'avg_ru'       => $avgRu,
            'avg_ua'       => $avgUa,
            'leaders_ua'   => $leadersUa,
            'leaders_ru'   => $leadersRu,
            'contested'    => $contested,
        ];
    }
}
