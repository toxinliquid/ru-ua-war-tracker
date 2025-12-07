<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @php
        $pageTitle = $post->title
            ? "{$post->title} | " . config('app.name')
            : __('War Post #:id', ['id' => $post->id]) . ' | ' . config('app.name');
    @endphp

    @include('partials.head', ['title' => $pageTitle])
</head>
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<body class="min-h-screen bg-gradient-to-b from-zinc-900 via-zinc-950 to-black text-zinc-100">
@include('partials.public-nav')
<div class="px-6 py-10 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-5xl flex-col gap-10">
        <header class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-300 transition hover:text-white">
                    <flux:icon.arrow-left class="size-4" />
                    {{ __('Back home') }}
                </a>

                <flux:badge variant="outline">
                    {{ __('Post #:id', ['id' => $post->id]) }}
                </flux:badge>
            </div>

            <div class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    {{ $post->title ?? __('Conflict Update') }}
                </h1>
                <p class="text-sm text-zinc-300">
                    {{ optional($post->date_from)->toFormattedDateString() }}
                    &mdash;
                    {{ optional($post->date_to)->toFormattedDateString() }}
                    @if ($post->day_from || $post->day_to)
                        <span class="ms-3 inline-flex items-center gap-2 rounded-full border border-zinc-700 px-3 py-1 text-xs uppercase tracking-wide text-zinc-300">
                                    <span class="size-2 rounded-full bg-emerald-400"></span>
                                    {{ __('War Day :from–:to', ['from' => $post->day_from ?? '?', 'to' => $post->day_to ?? '?']) }}
                                </span>
                    @endif
                </p>
            </div>

            @if ($post->description)
                <p class="max-w-3xl text-base leading-relaxed text-zinc-200">
                    {!! nl2br(e($post->description)) !!}
                </p>
            @endif
        </header>

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-5 shadow-sm backdrop-blur">
                <div class="text-xs uppercase tracking-[0.2em] text-emerald-300">{{ __('Ukrainian Advance') }}</div>
                <div class="mt-3 text-3xl font-semibold text-emerald-100">
                    {{ number_format($totals['ukraine'], 2) }} <span class="text-base font-medium text-emerald-200">km²</span>
                </div>
                @if (!is_null($post->overall_ukrainian_advance_km2))
                    <div class="mt-2 text-sm text-emerald-200">
                        {{ __('Overall (set): :value km²', ['value' => number_format((float) $post->overall_ukrainian_advance_km2, 2)]) }}
                    </div>
                @endif
            </div>
            <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 p-5 shadow-sm backdrop-blur">
                <div class="text-xs uppercase tracking-[0.2em] text-rose-300">{{ __('Russian Advance') }}</div>
                <div class="mt-3 text-3xl font-semibold text-rose-100">
                    {{ number_format($totals['russia'], 2) }} <span class="text-base font-medium text-rose-200">km²</span>
                </div>
            </div>
            <div class="rounded-2xl border border-sky-500/30 bg-sky-500/10 p-5 shadow-sm backdrop-blur">
                <div class="text-xs uppercase tracking-[0.2em] text-sky-300">{{ __('Net Change') }}</div>
                <div class="mt-3 text-3xl font-semibold text-sky-100">
                    {{ number_format($net, 2) }} <span class="text-base font-medium text-sky-200">km²</span>
                </div>
            </div>
        </section>

        <section class="space-y-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-baseline sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">{{ __('Situation Snapshots') }}</h2>
                    <p class="text-sm text-zinc-300">{{ __('Imagery, commentary, and territorial changes from the period.') }}</p>
                </div>
                <div class="text-sm text-zinc-400">
                    @php
                        $itemCount = $post->items->count();
                    @endphp
                    {{ $itemCount }} {{ \Illuminate\Support\Str::plural(__('entry'), $itemCount) }}
                </div>
            </div>

            <div class="space-y-8">
                @if ($post->items->count() > 0)
                    @foreach ($post->items as $item)
                        @php
                            if ($item->side === 'russia') {
                                $badgeLabel = __('Russian Forces');
                                $badgeClass = 'border-rose-500/30 bg-rose-500/10 text-rose-200';
                            } elseif ($item->side === 'ukraine') {
                                $badgeLabel = __('Ukrainian Forces');
                                $badgeClass = 'border-emerald-500/30 bg-emerald-500/10 text-emerald-200';
                            } else {
                                $badgeLabel = __('Context');
                                $badgeClass = 'border-zinc-500/30 bg-zinc-500/10 text-zinc-200';
                            }
                        @endphp

                        <article class="overflow-hidden rounded-3xl border border-white/5 bg-white/5 shadow-2xl shadow-emerald-500/10 backdrop-blur">
                            @if ($item->image_path)
                                <figure class="aspect-[3/2] overflow-hidden bg-black">
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($item->image_path) }}"
                                        alt="{{ $item->image_alt ?? '' }}"
                                        class="h-full w-full object-cover object-center transition duration-700 ease-out hover:scale-[1.02]"
                                    />
                                </figure>
                            @endif

                            <div class="space-y-4 px-6 py-6 sm:px-10 sm:py-8">
                                <div class="flex flex-wrap items-center gap-3">
                                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wider {{ $badgeClass }}">
                                                <span class="size-2 rounded-full bg-current/60"></span>
                                                {{ $badgeLabel }}
                                            </span>
                                    @if ($item->advance_km2 !== null)
                                        <span class="text-sm text-zinc-300">
                                                    {{ __('Advance: :value km²', ['value' => number_format((float) $item->advance_km2, 2)]) }}
                                                </span>
                                    @endif
                                </div>

                                @if ($item->short_description)
                                    <h3 class="text-xl font-semibold text-white">
                                        {{ $item->short_description }}
                                    </h3>
                                @endif

                                @if ($item->long_description)
                                    <div class="prose prose-invert max-w-none text-sm leading-relaxed text-zinc-200">
                                        {!! nl2br(e($item->long_description)) !!}
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                @else
                    <div class="rounded-2xl border border-dashed border-zinc-700 bg-white/5 p-10 text-center text-zinc-300">
                        {{ __('No entries were added for this period.') }}
                    </div>
                @endif
            </div>
        </section>

        <footer class="border-t border-white/10 pt-6 text-xs text-zinc-400">
            {{ __('Last updated') }} {{ optional($post->updated_at)->diffForHumans() }}
        </footer>
    </div>
</div>

@fluxScripts
</body>
</html>
