<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head', ['title' => __('Media Reactions & Articles')])
</head>
<body class="min-h-screen bg-gradient-to-b from-neutral-950 via-zinc-900 to-black text-zinc-100">
@include('partials.public-nav')
<div class="px-6 py-10 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-6xl flex-col gap-10">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                    {{ __('Curated western coverage') }}
                </div>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    {{ __('Media reactions & articles') }}
                </h1>
                <p class="text-sm text-zinc-300">
                    {{ __('Browse saved articles by tone or keyword, and jump straight to the source.') }}
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
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-zinc-200" for="tone">{{ __('Tone') }}</label>
                    <select id="tone" name="tone" class="mt-2 w-full rounded-xl border border-white/10 bg-zinc-900/70 px-4 py-2 text-sm text-white shadow-inner outline-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-500/40">
                        <option value="">{{ __('All tones') }}</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(request('tone') === $value)>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-zinc-200" for="search">{{ __('Search') }}</label>
                    <div class="mt-2 flex rounded-xl border border-white/10 bg-zinc-900/70 px-3 py-2 text-sm text-white shadow-inner focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/40">
                        <flux:icon.magnifying-glass class="size-4 text-zinc-400" />
                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="{{ __('Filter by title or URL') }}"
                            class="ml-2 w-full bg-transparent text-sm text-white placeholder:text-zinc-500 focus:outline-none"
                        >
                    </div>
                </div>

                <div class="md:col-span-3 flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40 bg-emerald-500/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-500/20">
                        <flux:icon.adjustments-vertical class="size-4" />
                        {{ __('Apply filters') }}
                    </button>
                    <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                        <flux:icon.arrow-path class="size-4" />
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold">{{ __('Articles') }}</h2>
                <div class="text-xs uppercase tracking-[0.25em] text-zinc-400">
                    {{ $articles->total() }} {{ __('entries') }}
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($articles as $article)
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
                        {{ __('No articles found for these filters.') }}
                    </div>
                @endforelse
            </div>

            @if ($articles->hasPages())
                <div>
                    {{ $articles->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

@fluxScripts
</body>
</html>
