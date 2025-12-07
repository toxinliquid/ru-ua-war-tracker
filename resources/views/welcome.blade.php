<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => __('SMO Situation Overview')])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-neutral-950 via-zinc-900 to-black text-zinc-100">
        @include('partials.public-nav')

        <div class="px-6 py-12 sm:px-10 lg:px-16">
            <header class="mx-auto flex max-w-5xl flex-col gap-6 text-center">
                <div class="inline-flex items-center gap-2 self-center rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                    {{ __('Latest frontline intelligence') }}
                </div>
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                    {{ __('Daily overview of the Russo-Ukrainian frontline') }}
                </h1>
                <p class="text-sm text-zinc-300 sm:text-base">
                    {{ __('Track headline advances, city control, and regional shifts at a glance. Dive deeper into detailed reports, or explore the interactive dashboards for granular analysis.') }}
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('frontline.control') }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20">
                        <flux:icon.layout-grid class="size-4" />
                        {{ __('Open control dashboard') }}
                    </a>
                    <a href="{{ route('cities.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                        <flux:icon.building-office class="size-4" />
                        {{ __('City breakdown') }}
                    </a>
                    <a href="{{ route('regions.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                        <flux:icon.globe-alt class="size-4" />
                        {{ __('Region breakdown') }}
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full border border-amber-300/40 bg-amber-400/10 px-4 py-2 text-sm font-semibold text-amber-100 transition hover:bg-amber-400/20">
                        <flux:icon.chart-bar-square class="size-4" />
                        {{ __('Article statistics dashboard') }}
                    </a>
                </div>
            </header>

            <main class="mx-auto mt-14 flex max-w-6xl flex-col gap-12">
                <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-5 text-center shadow-lg">
                        <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Tracked cities') }}</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $cityStats['count'] }}</p>
                        <p class="mt-1 text-xs text-zinc-400">{{ __('Average UA control') }}: <span class="text-emerald-300 font-semibold">{{ number_format($cityStats['avg_ua'], 1) }}%</span></p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-5 text-center shadow-lg">
                        <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Tracked regions') }}</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $regionStats['count'] }}</p>
                        <p class="mt-1 text-xs text-zinc-400">{{ __('Average RU control') }}: <span class="text-rose-300 font-semibold">{{ number_format($regionStats['avg_ru'], 1) }}%</span></p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-5 text-center shadow-lg">
                        <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Total Reports') }}</p>
                        <p class="mt-3 text-3xl font-semibold text-white">{{ $warStats['total_posts'] }}</p>
                        <p class="mt-1 text-xs text-zinc-400">{{ __('Latest update') }}: <span class="font-semibold text-emerald-200">{{ $warStats['latest_period'] ?? __('–') }}</span></p>
                    </article>
                    <article class="rounded-3xl border border-white/10 bg-white/5 p-5 text-center shadow-lg">
                        <p class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Cumulative gains') }}</p>
                        <div class="mt-3 grid gap-1 text-xs text-zinc-400">
                            <span>{{ __('Ukraine') }}: <span class="text-emerald-300 font-semibold">{{ number_format($warStats['total_ua_km2'], 1) }} km²</span></span>
                            <span>{{ __('Russia') }}: <span class="text-rose-300 font-semibold">{{ number_format($warStats['total_ru_km2'], 1) }} km²</span></span>
                        </div>
                    </article>
                </section>

                <section aria-labelledby="latest-reports" class="space-y-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 id="latest-reports" class="text-3xl font-semibold">{{ __('Latest frontline reports') }}</h2>
                            <p class="text-sm text-zinc-400">{{ __('Brief summaries from the most recent situation reports.') }}</p>
                        </div>
                        <a href="{{ route('war-posts.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                            {{ __('View all Advances Reports') }}
                            <flux:icon.arrow-top-right-on-square class="size-4" />
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($latestPosts as $post)
                            <article class="flex h-full flex-col rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg transition hover:border-emerald-400/50 hover:bg-white/[0.08]">
                                <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-zinc-400">
                                    <span>{{ optional($post->date_from)->toFormattedDateString() }} – {{ optional($post->date_to)->toFormattedDateString() }}</span>
                                    @if ($post->day_from || $post->day_to)
                                        <span>{{ __('Day :from–:to', ['from' => $post->day_from ?? '?', 'to' => $post->day_to ?? '?']) }}</span>
                                    @endif
                                </div>

                                <h3 class="mt-4 text-xl font-semibold text-white">
                                    {{ $post->title ?? __('War Post #:id', ['id' => $post->id]) }}
                                </h3>

                                @if ($post->description)
                                    <p class="mt-3 text-sm leading-relaxed text-zinc-300">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->description), 160) }}
                                    </p>
                                @endif

                                <dl class="mt-4 grid grid-cols-2 gap-3 text-xs text-zinc-400">
                                    <div>
                                        <dt class="uppercase tracking-[0.25em]">{{ __('UA gains') }}</dt>
                                        <dd class="mt-1 text-emerald-300 font-semibold">{{ number_format($post->total_ukrainian_gross_km2, 2) }} km²</dd>
                                    </div>
                                    <div>
                                        <dt class="uppercase tracking-[0.25em]">{{ __('RU gains') }}</dt>
                                        <dd class="mt-1 text-rose-300 font-semibold">{{ number_format($post->total_russian_gross_km2, 2) }} km²</dd>
                                    </div>
                                </dl>

                        <a href="{{ route('war-posts.show', $post) }}" class="mt-auto inline-flex items-center gap-2 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                                    {{ __('Read full report') }}
                                    <flux:icon.arrow-top-right-on-square class="size-4" />
                                </a>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 p-10 text-center text-zinc-400 sm:col-span-2 lg:col-span-3">
                                {{ __('No reports have been published yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>

                <section aria-labelledby="articles" class="space-y-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 id="articles" class="text-3xl font-semibold">{{ __('Media reactions & articles') }}</h2>
                            <p class="text-sm text-zinc-400">{{ __('Curated western coverage by tone.') }}</p>
                        </div>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                            {{ __('View all articles') }}
                            <flux:icon.arrow-top-right-on-square class="size-4" />
                        </a>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($latestArticles as $article)
                            @php
                                $toneLabel = \App\Models\WestNews::LINK_TYPES[$article->link_type] ?? ucfirst($article->link_type);
                                $toneColors = match($article->link_type) {
                                    'humiliated' => 'border-amber-400/30 bg-amber-500/10 text-amber-100',
                                    'spinning'   => 'border-sky-400/30 bg-sky-500/10 text-sky-100',
                                    'reeling'    => 'border-rose-400/30 bg-rose-500/10 text-rose-100',
                                    'sick'       => 'border-zinc-400/30 bg-zinc-500/10 text-zinc-100',
                                    'dead'       => 'border-zinc-400/30 bg-zinc-500/10 text-zinc-100',
                                    'weaponized' => 'border-emerald-400/30 bg-emerald-500/10 text-emerald-100',
                                    'russia_collapsing' => 'border-orange-400/30 bg-orange-500/10 text-orange-100',
                                    'russia_invade_europe' => 'border-purple-400/30 bg-purple-500/10 text-purple-100',
                                    default      => 'border-white/20 bg-white/5 text-white',
                                };
                            @endphp
                            <article class="flex h-full flex-col rounded-3xl border border-white/10 bg-white/[0.05] p-5 shadow-lg transition hover:border-emerald-400/50 hover:bg-white/[0.08]">
                                <div class="flex items-center justify-between text-xs text-zinc-400">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 {{ $toneColors }}">
                                        <span class="size-2 rounded-full bg-current/60"></span>
                                        {{ __($toneLabel) }}
                                    </span>
                                    <span>{{ optional($article->created_at)->diffForHumans() }}</span>
                                </div>

                                <h3 class="mt-4 text-lg font-semibold text-white line-clamp-2">{{ $article->title }}</h3>

                                <p class="mt-2 text-sm text-emerald-200 break-words">{{ \Illuminate\Support\Str::limit($article->url, 80) }}</p>

                                <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="mt-auto inline-flex items-center gap-2 pt-4 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                                    {{ __('Open article') }}
                                    <flux:icon.arrow-top-right-on-square class="size-4" />
                                </a>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/10 p-10 text-center text-zinc-400 sm:col-span-2 lg:col-span-3">
                                {{ __('No articles added yet.') }}
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="grid gap-6 lg:grid-cols-2">
                    <article class="rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg">
                        <header class="flex items-center justify-between gap-2">
                            <div>
                                <h3 class="text-2xl font-semibold text-white">{{ __('City control highlights') }}</h3>
                                <p class="text-xs text-zinc-400 uppercase tracking-[0.3em]">{{ __('Urban focus') }}</p>
                            </div>
                            <a href="{{ route('cities.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                                {{ __('View all cities') }}
                                <flux:icon.arrow-top-right-on-square class="size-4" />
                            </a>
                        </header>

                        <div class="mt-5 grid gap-4 text-sm text-zinc-300">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Ukrainian lead') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($cityStats['leaders_ua'] as $city)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $city->name }}</span>
                                            <span class="font-semibold text-emerald-300">{{ number_format($city->ukrainian_control_percent, 1) }}%</span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Russian lead') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($cityStats['leaders_ru'] as $city)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $city->name }}</span>
                                            <span class="font-semibold text-rose-300">{{ number_format($city->russian_control_percent, 1) }}%</span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Most contested') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($cityStats['contested'] as $city)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $city->name }}</span>
                                            <span class="font-semibold text-zinc-300">
                                                {{ number_format($city->ukrainian_control_percent, 1) }}% / {{ number_format($city->russian_control_percent, 1) }}%
                                            </span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg">
                        <header class="flex items-center justify-between gap-2">
                            <div>
                                <h3 class="text-2xl font-semibold text-white">{{ __('Regional control highlights') }}</h3>
                                <p class="text-xs text-zinc-400 uppercase tracking-[0.3em]">{{ __('Oblast overview') }}</p>
                            </div>
                            <a href="{{ route('regions.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-200 transition hover:text-sky-100">
                                {{ __('View all regions') }}
                                <flux:icon.arrow-top-right-on-square class="size-4" />
                            </a>
                        </header>

                        <div class="mt-5 grid gap-4 text-sm text-zinc-300">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Ukrainian lead') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($regionStats['leaders_ua'] as $region)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $region->name }}</span>
                                            <span class="font-semibold text-emerald-300">{{ number_format($region->ukrainian_control_percent, 1) }}%</span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Russian lead') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($regionStats['leaders_ru'] as $region)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $region->name }}</span>
                                            <span class="font-semibold text-rose-300">{{ number_format($region->russian_control_percent, 1) }}%</span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>

                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">{{ __('Most contested') }}</p>
                                <ul class="mt-2 space-y-2">
                                    @forelse ($regionStats['contested'] as $region)
                                        <li class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-2">
                                            <span>{{ $region->name }}</span>
                                            <span class="font-semibold text-zinc-300">
                                                {{ number_format($region->ukrainian_control_percent, 1) }}% / {{ number_format($region->russian_control_percent, 1) }}%
                                            </span>
                                        </li>
                                    @empty
                                        <li class="rounded-xl border border-dashed border-white/10 px-4 py-2 text-zinc-500">{{ __('No data available yet.') }}</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </article>
                </section>
            </main>

            @php
                $topCountries = \App\Models\Visit::select('country', \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT ip) as unique_visitors'))
                    ->groupBy('country')
                    ->orderByDesc('unique_visitors')
                    ->take(5)
                    ->get();
            @endphp

            <footer class="mx-auto mt-20 max-w-5xl border-t border-white/10 pt-8 text-center text-xs text-zinc-500 space-y-6">


                <div class="space-y-3">
                    <div class="text-zinc-400">{{ __('visitor countries') }}</div>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        @forelse ($topCountries as $country)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center text-sm text-white">
                                <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">{{ $country->country ?? __('Unknown') }}</div>
                                <div class="mt-2 text-xl font-semibold">{{ number_format($country->unique_visitors) }}</div>
                                <div class="text-xs text-zinc-400">{{ __('Visitors') }}</div>
                            </div>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-white/10 p-6 text-center text-zinc-400">
                                {{ __('No visitor data yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4">

                    <!-- Tox (Oman) -->
                    <a href="https://buymeacoffee.com/toxinliquid"
                       class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-5 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20 hover:text-emerald-100">

                        <img src="{{ asset('om.svg') }}"
                             alt="Oman Flag"
                             class="h-4 w-6 rounded-sm object-cover" />

                        <flux:icon.currency-dollar class="size-4" />
                        {{ __('Tip Tox (Site Maintainer)') }}
                    </a>

                    <!-- Heyden (Australia) -->
                    <a href="https://buymeacoffee.com/heyheyhayden"
                       class="inline-flex items-center gap-2 rounded-full border border-sky-400/40 bg-sky-500/10 px-5 py-2 text-sm font-semibold text-sky-200 transition hover:bg-sky-500/20 hover:text-sky-100">

                        <img src="{{ asset('au.svg') }}"
                             alt="Australia Flag"
                             class="h-4 w-6 rounded-sm object-cover" />

                        <flux:icon.currency-dollar class="size-4" />
                        {{ __('Tip HeyHeyHayden (Report Compiler)') }}
                    </a>

                </div>

            </footer>

        </div>

        @fluxScripts
    </body>
</html>
