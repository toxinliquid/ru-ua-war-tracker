<x-layouts.app :title="__('Visitor Countries')">
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div>
                <flux:heading size="lg">{{ __('Visitor country stats') }}</flux:heading>
                <flux:text class="text-zinc-600 dark:text-zinc-400">{{ __('Unique visitors are counted by distinct IPs per country.') }}</flux:text>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-300">
                <div>{{ __('Total hits') }}: <strong>{{ number_format($totals['hits']) }}</strong></div>
                <div>{{ __('Unique IPs') }}: <strong>{{ number_format($totals['unique']) }}</strong></div>
            </div>
        </div>

        <flux:card>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-zinc-200 dark:border-zinc-800">
                        <tr class="text-left text-xs uppercase tracking-[0.25em] text-zinc-500 dark:text-zinc-400">
                            <th class="py-3 px-4">{{ __('Country') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Unique IPs') }}</th>
                            <th class="py-3 px-4 text-right">{{ __('Total hits') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($countries as $country)
                            <tr>
                                <td class="py-3 px-4 text-zinc-900 dark:text-zinc-100">{{ $country->country ?? __('Unknown') }}</td>
                                <td class="py-3 px-4 text-right font-mono text-zinc-900 dark:text-zinc-100">{{ number_format($country->unique_ips) }}</td>
                                <td class="py-3 px-4 text-right font-mono text-zinc-900 dark:text-zinc-100">{{ number_format($country->hits) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 px-4 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No visit data available.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
