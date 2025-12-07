<div class="space-y-6">
    @persist('toast')
    <flux:toast />
    @endpersist

    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <flux:heading size="lg">{{ __('West News Feed') }}</flux:heading>
            <flux:text class="mt-1">
                {{ __('Curate notable western media reactions. Select a tone, add a title, and link to the source.') }}
            </flux:text>
        </div>
        <flux:button wire:click="create" icon="plus">{{ __('Add Entry') }}</flux:button>
    </div>

    <flux:card class="space-y-6">
        <form wire:submit.prevent="save" class="space-y-4">
            @if ($editingId)
                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                {{ __('Editing entry') }}
                            </h3>
                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                {{ __('Update the details below and click save to confirm your changes.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <flux:input
                wire:model="title"
                :label="__('Title')"
                placeholder="e.g., Western outlet praises Ukrainian gains"
                autofocus
            />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:select wire:model="link_type" :label="__('Link tone')">
                    @foreach ($types as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ __($label) }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input
                    wire:model="url"
                    type="url"
                    :label="__('Link URL')"
                    placeholder="https://example.com/article"
                />
            </div>

            <div class="flex justify-end gap-2">
                @if ($editingId)
                    <flux:button type="button" variant="ghost" wire:click="create">
                        {{ __('Cancel') }}
                    </flux:button>
                @endif

                <flux:button type="submit" icon="check">
                    {{ $editingId ? __('Save entry') : __('Add entry') }}
                </flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <flux:input
                wire:model.debounce.300ms="search"
                type="search"
                icon="magnifying-glass"
                :label="__('Search entries')"
                :placeholder="__('Search by title or URL')"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('Title') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Type') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Link') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Updated') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($entries as $entry)
                    <tr>
                        <td class="px-4 py-3 align-top font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $entry->title }}
                        </td>
                        <td class="px-4 py-3 align-top text-zinc-500 dark:text-zinc-300">
                            {{ __(\App\Models\WestNews::LINK_TYPES[$entry->link_type] ?? ucfirst($entry->link_type)) }}
                        </td>
                        <td class="px-4 py-3 align-top">
                            <a
                                href="{{ $entry->url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-emerald-500 hover:text-emerald-400 dark:text-emerald-300"
                            >
                                {{ str($entry->url)->limit(60) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 align-top text-zinc-500 dark:text-zinc-300">
                            {{ optional($entry->updated_at)->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 align-top text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="pencil-square"
                                    wire:click="edit({{ $entry->id }})"
                                >
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button
                                    size="xs"
                                    variant="danger"
                                    icon="trash"
                                    x-data
                                    x-on:click.prevent="if (confirm('{{ __('Delete this entry?') }}')) $wire.delete({{ $entry->id }});"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                            {{ __('No entries yet. Add your first entry above.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <div>
                {{ $entries->links() }}
            </div>
        @endif
    </flux:card>
</div>
