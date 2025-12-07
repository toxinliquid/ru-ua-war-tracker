<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head', ['title' => __('SMO Advances Reports')])
</head>
<body class="min-h-screen bg-gradient-to-b from-neutral-950 via-zinc-900 to-black text-zinc-100">
@include('partials.public-nav')
<div class="px-6 py-10 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-6xl flex-col gap-10">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                    {{ __('Frontline reports archive') }}
                </div>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    {{ __('All SMO Advances Reports') }}
                </h1>
                <p class="text-sm text-zinc-300">
                    {{ __('Search and filter situation reports by date range, war day, or keyword.') }}
                </p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    <flux:icon.arrow-left class="size-4" />
                    {{ __('Back home') }}
                </a>
            </div>
        </header>

        <section class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-lg">
            <form method="get" class="grid gap-4 md:grid-cols-3 md:items-end">
                <div class="md:col-span-3">
                    <label class="text-sm font-medium text-zinc-200" for="search">{{ __('Search') }}</label>
                    <div class="mt-2 flex rounded-xl border border-white/10 bg-zinc-900/70 px-3 py-2 text-sm text-white shadow-inner focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/40">
                        <flux:icon.magnifying-glass class="size-4 text-zinc-400" />
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Filter by title or description') }}"
                            class="ml-2 w-full bg-transparent text-sm text-white placeholder:text-zinc-500 focus:outline-none"
                        >
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-zinc-200" for="date_from">{{ __('Date from') }}</label>
                    <input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}" class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2 text-sm text-white shadow-inner outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/40" />
                </div>

                <div>
                    <label class="text-sm font-medium text-zinc-200" for="date_to">{{ __('Date to') }}</label>
                    <input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}" class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2 text-sm text-white shadow-inner outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/40" />
                </div>

                <div class="grid grid-cols-2 gap-4 md:col-span-3 lg:col-span-1 lg:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-zinc-200" for="day_from">{{ __('War day from') }}</label>
                        <input id="day_from" name="day_from" type="number" min="1" value="{{ request('day_from') }}" class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2 text-sm text-white shadow-inner outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/40" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-zinc-200" for="day_to">{{ __('War day to') }}</label>
                        <input id="day_to" name="day_to" type="number" min="1" value="{{ request('day_to') }}" class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2 text-sm text-white shadow-inner outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/40" />
                    </div>
                </div>

                <div class="md:col-span-3 flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20">
                        <flux:icon.adjustments-vertical class="size-4" />
                        {{ __('Apply filters') }}
                    </button>
                    <a href="{{ route('war-posts.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                        <flux:icon.arrow-path class="size-4" />
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold">{{ __('SMO Advances Reports') }}</h2>
                <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">
                    {{ $posts->total() }} {{ __('entries') }}
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    <article class="flex h-full flex-col rounded-3xl border border-white/10 bg-white/[0.05] p-6 shadow-lg transition hover:border-emerald-400/50 hover:bg-white/[0.08]">
                        <div class="flex items-center justify-between text-xs uppercase tracking-[0.3em] text-zinc-400">
                            <span>{{ optional($post->date_from)->toFormattedDateString() }} – {{ optional($post->date_to)->toFormattedDateString() }}</span>
                            @if ($post->day_from || $post->day_to)
                                <span>{{ __('Day :from–:to', ['from' => $post->day_from ?? '?', 'to' => $post->day_to ?? '?']) }}</span>
                            @endif
                        </div>

                        <h3 class="mt-4 text-xl font-semibold text-white line-clamp-2">
                            {{ $post->title ?? __('War Post #:id', ['id' => $post->id]) }}
                        </h3>

                        @if ($post->description)
                            <p class="mt-3 text-sm leading-relaxed text-zinc-300 line-clamp-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->description), 180) }}
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

                        <a href="{{ route('war-posts.show', $post) }}" class="mt-auto inline-flex items-center gap-2 pt-4 text-sm font-semibold text-emerald-200 transition hover:text-emerald-100">
                            {{ __('Open report') }}
                            <flux:icon.arrow-top-right-on-square class="size-4" />
                        </a>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-white/10 p-10 text-center text-zinc-400 sm:col-span-2 lg:col-span-3">
                        {{ __('No posts found for these filters.') }}
                    </div>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div>
                    {{ $posts->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

@fluxScripts
</body>
</html>
