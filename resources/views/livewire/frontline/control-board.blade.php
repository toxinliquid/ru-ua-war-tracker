<div class="px-6 py-12 sm:px-10 lg:px-16">
    <div class="mx-auto flex max-w-6xl flex-col gap-12">
        <header class="space-y-6 text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.25em] text-zinc-300">
                {{ __('Interactive overview') }}
            </div>
            <div class="space-y-3">
                <h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">
                    {{ __('Frontline Control Dashboard') }}
                </h1>
                <p class="mx-auto max-w-3xl text-sm text-zinc-300 sm:text-base">
                    {{ __('Explore city and regional control with live filters. Switch views, search specific locations, and highlight areas dominated by either side or contested zones.') }}
                </p>
            </div>
        </header>

        <section class="rounded-3xl border border-white/10 bg-white/[0.04] p-6 shadow-lg backdrop-blur">
            <div class="grid gap-4 md:grid-cols-4 md:items-center">
                <div class="md:col-span-2">
                    <span class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('View scope') }}</span>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (['all' => __('All entries'), 'cities' => __('Cities'), 'regions' => __('Regions')] as $value => $label)
                            <flux:button
                                size="xs"
                                :variant="$scope === $value ? 'primary' : 'ghost'"
                                wire:click="$set('scope', '{{ $value }}')"
                            >
                                {{ $label }}
                            </flux:button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Dominance filter') }}</span>
                    <flux:select wire:model="dominance" class="mt-2">
                        <flux:select.option value="any">{{ __('Any') }}</flux:select.option>
                        <flux:select.option value="russia">{{ __('Russian majority') }}</flux:select.option>
                        <flux:select.option value="ukraine">{{ __('Ukrainian majority') }}</flux:select.option>
                        <flux:select.option value="contested">{{ __('Contested / mixed') }}</flux:select.option>
                    </flux:select>
                </div>

                <div>
                    <span class="text-xs uppercase tracking-[0.3em] text-zinc-400">{{ __('Sort by') }}</span>
                    <flux:select wire:model="sort" class="mt-2">
                        <flux:select.option value="name">{{ __('Name (A–Z)') }}</flux:select.option>
                        <flux:select.option value="ru_desc">{{ __('Russian control (high → low)') }}</flux:select.option>
                        <flux:select.option value="ua_desc">{{ __('Ukrainian control (high → low)') }}</flux:select.option>
                        <flux:select.option value="neutral_desc">{{ __('Contested (high → low)') }}</flux:select.option>
                        <flux:select.option value="updated_desc">{{ __('Last updated (newest)') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <flux:input
                    wire:model.debounce.300ms="search"
                    type="search"
                    icon="magnifying-glass"
                    class="w-full md:w-72"
                    :placeholder="__('Search by name…')"
                />

                <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">
                    {{ __('Visible entries') }}:
                    <span class="ms-1 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-sm font-semibold text-white">
                        {{ $summary['visible_count'] }}
                    </span>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-live="polite">
            @forelse ($items as $item)
                <article class="group rounded-3xl border border-white/5 bg-white/[0.04] p-5 shadow-inner shadow-emerald-500/5 transition hover:border-emerald-500/40 hover:bg-white/[0.08]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] uppercase tracking-[0.25em] text-zinc-300">
                                {{ $item['type'] === 'city' ? __('City') : __('Region') }}
                            </span>
                            <h2 class="mt-3 text-xl font-semibold text-white">{{ $item['name'] }}</h2>
                            <p class="text-xs uppercase tracking-[0.25em] text-zinc-500">
                                {{ optional($item['updated'])->diffForHumans() ?? __('recently') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="block text-xs uppercase tracking-[0.3em] text-emerald-200">{{ __('UA') }}</span>
                            <span class="text-2xl font-semibold text-emerald-100">
                                {{ number_format($item['ua'], 1) }}%
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        <div class="relative h-3 overflow-hidden rounded-full bg-zinc-800">
                            <div class="absolute inset-y-0 left-0 bg-rose-500/80" style="width: {{ $item['ru'] }}%;"></div>
                            <div class="absolute inset-y-0 bg-emerald-500/80" style="width: {{ $item['ua'] }}%; left: {{ $item['ru'] }}%;"></div>
                            @if ($item['neutral'] > 0)
                                <div class="absolute inset-y-0 bg-zinc-600/70" style="width: {{ $item['neutral'] }}%; left: {{ $item['ru'] + $item['ua'] }}%;"></div>
                            @endif
                        </div>
                        <div class="flex justify-between text-xs text-zinc-400">
                            <span>{{ __('RU') }} {{ number_format($item['ru'], 1) }}%</span>
                            @if ($item['neutral'] > 0)
                                <span>{{ __('Contested') }} {{ number_format($item['neutral'], 1) }}%</span>
                            @endif
                            <span>{{ __('UA') }} {{ number_format($item['ua'], 1) }}%</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-white/10 p-10 text-center text-zinc-400">
                    {{ __('No locations match the current filters.') }}
                </div>
            @endforelse
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
