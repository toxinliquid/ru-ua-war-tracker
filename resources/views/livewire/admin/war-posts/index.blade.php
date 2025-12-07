<div class="space-y-6">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <flux:heading size="lg">{{ __('SMO Advances Reports') }}</flux:heading>
            <flux:text class="mt-1">
                {{ __('Review conflict updates and jump into editing existing entries.') }}
            </flux:text>
        </div>

        <flux:button :href="route('admin.war-posts.create')" icon="plus" wire:navigate>
            {{ __('New War Post') }}
        </flux:button>
    </div>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <flux:input
                wire:model.debounce.300ms="search"
                type="search"
                icon="magnifying-glass"
                :label="__('Search')"
                :placeholder="__('Search by title, description, or date')"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('Title') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Date Range') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Totals (km²)') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('Items') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($posts as $post)
                        <tr class="transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/60">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $post->title ?? __('Untitled post #:id', ['id' => $post->id]) }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-500 line-clamp-2 dark:text-zinc-400">
                                    {{ \Illuminate\Support\Str::limit($post->description, 110) ?: __('No description provided.') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 align-top text-zinc-700 dark:text-zinc-200">
                                <div>{{ optional($post->date_from)->toFormattedDateString() }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ __('to') }} {{ optional($post->date_to)->toFormattedDateString() }}
                                </div>
                                @if ($post->day_from || $post->day_to)
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('Day :from–:to', ['from' => $post->day_from ?? '?', 'to' => $post->day_to ?? '?']) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ __('RU: :value', ['value' => number_format((float) $post->total_russian_gross_km2, 2)]) }}
                                </div>
                                <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                    {{ __('UA: :value', ['value' => number_format((float) $post->total_ukrainian_gross_km2, 2)]) }}
                                </div>
                                @if (!is_null($post->overall_ukrainian_advance_km2))
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ __('UA Overall: :value', ['value' => number_format((float) $post->overall_ukrainian_advance_km2, 2)]) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top text-zinc-700 dark:text-zinc-200">
                                {{ $post->items_count }}
                            </td>
                            <td class="px-4 py-3 align-top text-right space-y-2">
                                <flux:button
                                    size="sm"
                                    variant="outline"
                                    icon="arrow-top-right-on-square"
                                    :href="route('war-posts.show', $post)"
                                >
                                    {{ __('View') }}
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil-square"
                                    :href="route('admin.war-posts.edit', $post)"
                                    wire:navigate
                                >
                                    {{ __('Edit') }}
                                </flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="space-y-2">
                                    <flux:heading size="md">{{ __('No Reports yet') }}</flux:heading>
                                    <flux:text>{{ __('Create your first update to track advances and images.') }}</flux:text>
                                    <flux:button :href="route('admin.war-posts.create')" icon="plus" wire:navigate>
                                        {{ __('Create War Post') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($posts->hasPages())
            <div>
                {{ $posts->links() }}
            </div>
        @endif
    </flux:card>
</div>
