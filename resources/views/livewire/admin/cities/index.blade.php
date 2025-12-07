<div class="space-y-6">
    @persist('toast')
    <flux:toast />
    @endpersist

    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <div>
            <flux:heading size="lg">{{ __('City Control Tracker') }}</flux:heading>
            <flux:text class="mt-1">
                {{ __('Manage frontline city control percentages. Enter either side and we will fill in the remainder.') }}
            </flux:text>
        </div>
        <flux:button wire:click="create" icon="plus">{{ __('Add New City') }}</flux:button>
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
                                {{ __('Editing :city', ['city' => $name]) }}
                            </h3>
                            <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                {{ __('Adjust the figures below and click save to update.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <flux:input
                wire:model="name"
                :label="__('City name')"
                placeholder="e.g., Avdiivka"
                autofocus
                data-city-name
            />

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input
                    wire:model="russian_control_percent"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    :label="__('Russian control (%)')"
                    placeholder="e.g., 30"
                />

                <flux:input
                    wire:model="ukrainian_control_percent"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    :label="__('Ukrainian control (%)')"
                    placeholder="e.g., 70"
                />
            </div>

            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ __('Leave one side blank and it will automatically fill to 100%. Values can leave room for contested areas if the total is below 100%.') }}
            </flux:text>

            <div class="flex justify-end gap-2">
                @if ($editingId)
                    <flux:button type="button" variant="ghost" wire:click="create">
                        {{ __('Cancel') }}
                    </flux:button>
                @endif

                <flux:button type="submit" icon="check">
                    {{ $editingId ? __('Save changes') : __('Add city') }}
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
                :label="__('Search cities')"
                :placeholder="__('Search by name')"
            />
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-700">
                <thead class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('City') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Russian control') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Ukrainian control') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Contested') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('Updated') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($cities as $city)
                    <tr>
                        <td class="px-4 py-3 align-top font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $city->name }}
                        </td>
                        <td class="px-4 py-3 align-top text-rose-500 dark:text-rose-300">
                            {{ number_format($city->russian_control_percent, 2) }}%
                        </td>
                        <td class="px-4 py-3 align-top text-emerald-500 dark:text-emerald-300">
                            {{ number_format($city->ukrainian_control_percent, 2) }}%
                        </td>
                        <td class="px-4 py-3 align-top text-zinc-500 dark:text-zinc-300">
                            {{ number_format($city->neutral_control_percent, 2) }}%
                        </td>
                        <td class="px-4 py-3 align-top text-zinc-500 dark:text-zinc-300">
                            {{ optional($city->updated_at)->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 align-top text-right">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    icon="pencil-square"
                                    wire:click="edit({{ $city->id }})"
                                >
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button
                                    size="xs"
                                    variant="danger"
                                    icon="trash"
                                    x-data
                                    x-on:click.prevent="if (confirm('{{ __('Delete :city?', ['city' => $city->name]) }}')) $wire.delete({{ $city->id }});"
                                >
                                    {{ __('Delete') }}
                                </flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                            {{ __('No cities tracked yet. Add your first city above.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($cities->hasPages())
            <div>
                {{ $cities->links() }}
            </div>
        @endif
    </flux:card>
</div>
