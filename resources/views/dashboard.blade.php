<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head', ['title' => __('Situation Dashboard')])
</head>
<body class="min-h-screen bg-gradient-to-b from-neutral-950 via-zinc-900 to-black text-zinc-100">
@include('partials.public-nav')
@php
    $warPostCount = \App\Models\WarPost::count();
    $latestPost   = \App\Models\WarPost::orderByDesc('date_to')->orderByDesc('id')->first();
    $totalUaKm2   = round((float) \App\Models\WarPost::sum('total_ukrainian_gross_km2'), 2);
    $totalRuKm2   = round((float) \App\Models\WarPost::sum('total_russian_gross_km2'), 2);

    $cityCount    = \App\Models\City::count();
    $cityAvgUa    = $cityCount ? round((float) \App\Models\City::avg('ukrainian_control_percent'), 1) : 0;
    $cityAvgRu    = $cityCount ? round((float) \App\Models\City::avg('russian_control_percent'), 1) : 0;

    $regionCount  = \App\Models\Region::count();
    $regionAvgUa  = $regionCount ? round((float) \App\Models\Region::avg('ukrainian_control_percent'), 1) : 0;
    $regionAvgRu  = $regionCount ? round((float) \App\Models\Region::avg('russian_control_percent'), 1) : 0;

    $spinningCount   = \App\Models\WestNews::where('link_type', 'spinning')->count();
    $humiliatedCount = \App\Models\WestNews::where('link_type', 'humiliated')->count();
    $reelingCount    = \App\Models\WestNews::where('link_type', 'reeling')->count();
    $sickCount       = \App\Models\WestNews::where('link_type', 'sick')->count();
    $deadCount       = \App\Models\WestNews::where('link_type', 'dead')->count();
    $collapsingCount = \App\Models\WestNews::where('link_type', 'russia_collapsing')->count();
    $invadeCount     = \App\Models\WestNews::where('link_type', 'russia_invade_europe')->count();
    $weaponizedCount = \App\Models\WestNews::where('link_type', 'weaponized')->count();
    $newsTotal       = max(1, $spinningCount + $humiliatedCount + $reelingCount + $sickCount + $deadCount + $collapsingCount + $invadeCount + $weaponizedCount);

    $visitTotal  = \App\Models\Visit::count();
    $visitUnique = \App\Models\Visit::distinct('ip')->count('ip');
    $topCountries = \App\Models\Visit::select('country', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT ip) as unique_visitors'))
        ->groupBy('country')
        ->orderByDesc('unique_visitors')
        ->take(5)
        ->get();
@endphp

<div class="px-6 py-10 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-6xl flex-col gap-10">
        <header class="space-y-4">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                {{ __('Situation Dashboard') }}
            </div>
            <div class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('Operational overview & audience stats') }}</h1>
                <p class="text-sm text-zinc-300">{{ __('Quick insight into reports, control metrics, media narratives, and visitors.') }}</p>
            </div>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('War posts') }}</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($warPostCount) }}</p>
                <p class="text-sm text-zinc-400">{{ __('Latest:') }} {{ optional($latestPost?->date_to)->toFormattedDateString() ?? '—' }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('UA gains (km²)') }}</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-200">{{ number_format($totalUaKm2, 1) }}</p>
                <p class="text-sm text-zinc-400">{{ __('Cumulative reported') }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('RU gains (km²)') }}</p>
                <p class="mt-3 text-3xl font-semibold text-rose-200">{{ number_format($totalRuKm2, 1) }}</p>
                <p class="text-sm text-zinc-400">{{ __('Cumulative reported') }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-lg">
                <p class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('Visitors') }}</p>
                <p class="mt-3 text-3xl font-semibold text-white">{{ number_format($visitUnique) }}</p>
                <p class="text-sm text-zinc-400">{{ __('Unique') }} · {{ number_format($visitTotal) }} {{ __('hits') }}</p>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">{{ __('Control averages') }}</h2>
                    <span class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('Live data') }}</span>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('Cities tracked') }}</div>
                        <div class="mt-2 text-2xl font-semibold text-white">{{ number_format($cityCount) }}</div>
                        <div class="mt-3 text-sm text-zinc-400">{{ __('UA avg:') }} <span class="text-emerald-200 font-semibold">{{ number_format($cityAvgUa, 1) }}%</span></div>
                        <div class="text-sm text-zinc-400">{{ __('RU avg:') }} <span class="text-rose-200 font-semibold">{{ number_format($cityAvgRu, 1) }}%</span></div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __('Regions tracked') }}</div>
                        <div class="mt-2 text-2xl font-semibold text-white">{{ number_format($regionCount) }}</div>
                        <div class="mt-3 text-sm text-zinc-400">{{ __('UA avg:') }} <span class="text-emerald-200 font-semibold">{{ number_format($regionAvgUa, 1) }}%</span></div>
                        <div class="text-sm text-zinc-400">{{ __('RU avg:') }} <span class="text-rose-200 font-semibold">{{ number_format($regionAvgRu, 1) }}%</span></div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">{{ __('Visitor countries') }}</h2>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    @forelse ($topCountries as $country)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center">
                            <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ $country->country ?? __('Unknown') }}</div>
                            <div class="mt-2 text-2xl font-semibold text-white">{{ number_format($country->unique_visitors) }}</div>
                            <div class="text-xs text-zinc-400">{{ __('Visitors') }}</div>
                        </div>
                    @empty
                        <div class="sm:col-span-2 rounded-2xl border border-dashed border-white/10 p-6 text-center text-zinc-400">
                            {{ __('No visit data available yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">{{ __('Media narratives (West News)') }}</h2>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ([
                    ['label' => 'Humiliated', 'count' => $humiliatedCount, 'color' => 'text-amber-200'],
                    ['label' => 'Spinning', 'count' => $spinningCount, 'color' => 'text-sky-200'],
                    ['label' => 'Reeling', 'count' => $reelingCount, 'color' => 'text-rose-200'],
                    ['label' => 'Sick', 'count' => $sickCount, 'color' => 'text-zinc-200'],
                    ['label' => 'Dead', 'count' => $deadCount, 'color' => 'text-zinc-200'],
                    ['label' => 'Weaponized', 'count' => $weaponizedCount, 'color' => 'text-emerald-200'],
                    ['label' => 'Russia Collapsing', 'count' => $collapsingCount, 'color' => 'text-orange-200'],
                    ['label' => 'Russia Invade Europe', 'count' => $invadeCount, 'color' => 'text-purple-200'],
                ] as $row)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center">
                        <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">{{ __($row['label']) }}</div>
                        <div class="mt-2 text-2xl font-semibold {{ $row['color'] }}">{{ number_format($row['count']) }}</div>
                        <div class="text-xs text-zinc-400">{{ __('Entries') }}</div>
                    </div>
                @endforeach
            </div>

            <div class="overflow-x-auto rounded-2xl border border-white/10 bg-white/5">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-white/10 text-left uppercase tracking-[0.2em] text-zinc-400">
                        <tr>
                            <th class="py-3 px-4">{{ __('Category') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Count') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Percentage') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10 text-zinc-200">
                        @foreach ([
                            ['label' => 'Spinning', 'count' => $spinningCount],
                            ['label' => 'Humiliated', 'count' => $humiliatedCount],
                            ['label' => 'Reeling', 'count' => $reelingCount],
                            ['label' => 'Sick', 'count' => $sickCount],
                            ['label' => 'Dead', 'count' => $deadCount],
                            ['label' => 'Weaponized', 'count' => $weaponizedCount],
                            ['label' => 'Russia Collapsing', 'count' => $collapsingCount],
                            ['label' => 'Russia Invade Europe', 'count' => $invadeCount],
                        ] as $row)
                            @php $percent = round(($row['count'] / $newsTotal) * 100, 1); @endphp
                            <tr>
                                <td class="py-3 px-4">{{ __($row['label']) }}</td>
                                <td class="py-3 px-4 text-right font-mono">{{ number_format($row['count']) }}</td>
                                <td class="py-3 px-4 text-right font-mono">{{ number_format($percent, 1) }}%</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold bg-white/5 text-white">
                            <td class="py-3 px-4">{{ __('Total') }}</td>
                            <td class="py-3 px-4 text-right font-mono">{{ number_format($newsTotal) }}</td>
                            <td class="py-3 px-4 text-right font-mono">100.0%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

@fluxScripts
</body>
</html>
