<div class="px-6 py-12 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-6xl flex-col gap-10">
        <header class="space-y-4 text-center sm:text-left">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                    {{ __('City control overview') }}
                </div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    <flux:icon.arrow-left class="size-4" />
                    {{ __('Back home') }}
                </a>
            </div>
            <div class="space-y-2">
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                    {{ __('Urban control snapshot') }}
                </h1>
                <p class="text-sm text-zinc-300 sm:text-base">
                    {{ __('Live estimates of city control between Russian and Ukrainian forces.') }}
                </p>
            </div>
            <div class="flex flex-wrap gap-3 text-xs uppercase tracking-[0.2em] text-zinc-400">
                <span class="inline-flex items-center gap-2">
                    <span class="size-2 rounded-full bg-rose-500"></span> {{ __('Russia') }}
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="size-2 rounded-full bg-emerald-500"></span> {{ __('Ukraine') }}
                </span>
                <span class="inline-flex items-center gap-2">
                    <span class="size-2 rounded-full bg-zinc-600"></span> {{ __('Contested / Unknown') }}
                </span>
            </div>
        </header>

        <section class="space-y-6" aria-label="{{ __('City control cards') }}">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-zinc-400">
                    {{ $cities->total() }} {{ __('cities tracked') }}
                    @if ($cities->hasPages())
                        <span class="ml-2 text-xs uppercase tracking-[0.2em]">{{ __('Page') }} {{ $cities->currentPage() }} / {{ $cities->lastPage() }}</span>
                    @endif
                </div>
                <div class="w-full sm:w-80">
                    <div class="flex rounded-full border border-white/10 bg-zinc-900/70 px-3 py-2 text-sm text-white shadow-inner focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-500/40">
                        <flux:icon.magnifying-glass class="size-4 text-zinc-400" />
                        <input
                            type="search"
                            placeholder="{{ __('Search cities or regions') }}"
                            wire:model.defer="search"
                            class="ml-2 w-full bg-transparent text-sm text-white placeholder:text-zinc-500 focus:outline-none"
                        />
                    </div>
                    <div class="mt-2 flex justify-end">
                        <flux:button size="xs" icon="check" wire:click="applySearch">{{ __('Apply') }}</flux:button>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @forelse ($cities as $city)
                    @php
                        $ru = max(0, min(100, $city->russian_control_percent));
                        $ua = max(0, min(100, $city->ukrainian_control_percent));
                        $neutral = max(0, 100 - ($ru + $ua));
                    @endphp
                    <article class="rounded-3xl border border-white/5 bg-white/[0.04] p-6 shadow-lg shadow-emerald-500/5 backdrop-blur">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-baseline sm:justify-between">
                            <div>
                                <h2 class="text-2xl font-semibold">
                                    {{ $city->name }}
                                </h2>
                                <p class="text-xs uppercase tracking-[0.28em] text-zinc-400">
                                    {{ __('Updated :time', ['time' => optional($city->updated_at)->diffForHumans() ?? __('recently')]) }}
                                </p>
                            </div>
                            <div class="flex flex-wrap gap-2 text-sm">
                                <span class="inline-flex items-center gap-2 rounded-full border border-rose-500/40 bg-rose-500/15 px-3 py-1 text-rose-200">
                                    {{ number_format($ru, 1) }}%
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/40 bg-emerald-500/15 px-3 py-1 text-emerald-200">
                                    {{ number_format($ua, 1) }}%
                                </span>
                                @if ($neutral > 0)
                                    <span class="inline-flex items-center gap-2 rounded-full border border-zinc-600/40 bg-zinc-600/20 px-3 py-1 text-zinc-300">
                                        {{ number_format($neutral, 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 space-y-2">
                            <div class="relative h-4 overflow-hidden rounded-full bg-zinc-800">
                                <div class="absolute inset-y-0 left-0 bg-rose-500/80" style="width: {{ $ru }}%;"></div>
                                <div class="absolute inset-y-0 bg-emerald-500/80" style="width: {{ $ua }}%; left: {{ $ru }}%;"></div>
                                @if ($neutral > 0)
                                    <div class="absolute inset-y-0 bg-zinc-600/70" style="width: {{ $neutral }}%; left: {{ $ru + $ua }}%;"></div>
                                @endif
                            </div>
                            <div class="flex justify-between text-xs text-zinc-400">
                                <span>{{ __('Russian control') }}</span>
                                <span>{{ __('Ukrainian control') }}</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="sm:col-span-2 rounded-2xl border border-dashed border-white/10 p-10 text-center text-zinc-400">
                        {{ __('No city control data available yet.') }}
                    </div>
                @endforelse
            </div>

            @if ($cities->hasPages())
                <div class="pt-2">
                    {{ $cities->links() }}
                </div>
            @endif
        </section>

        <footer class="mx-auto mt-20 max-w-5xl border-t border-white/10 pt-8 text-center text-xs text-zinc-500 space-y-4">



            <div class="flex items-center justify-center gap-4">

                <!-- Donate to Tox -->
                <a href="https://buymeacoffee.com/toxinliquid"
                   class="inline-flex items-center gap-2 rounded-full border border-emerald-400/40
                  bg-emerald-500/10 px-5 py-2 text-sm font-semibold text-emerald-200
                  transition hover:bg-emerald-500/20 hover:text-emerald-100">
                    <flux:icon.currency-dollar class="size-4" />
                    {{ __('tip to Tox, Site Maintainer') }}
                </a>

                <!-- Donate to Heyden -->
                <a href="https://buymeacoffee.com/heyheyhayden"
                   class="inline-flex items-center gap-2 rounded-full border border-sky-400/40
                  bg-sky-500/10 px-5 py-2 text-sm font-semibold text-sky-200
                  transition hover:bg-sky-500/20 hover:text-sky-100">
                    <flux:icon.currency-dollar class="size-4" />
                    {{ __('tip HeyHeyHayden report compiler,') }}
                </a>

            </div>

        </footer>
    </div>
</div>
