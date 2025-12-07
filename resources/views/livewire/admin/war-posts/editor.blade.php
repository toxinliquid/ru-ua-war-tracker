<div class="space-y-6">

    {{-- War Post --}}
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">War Post</flux:heading>
            <flux:text class="mt-1">Create or update a progress post</flux:text>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input
                wire:model.defer="title"
                label="Title (optional)"
                placeholder="e.g., Russian and Ukrainian advances from Day 1345–1346"
            />

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model.defer="date_from" type="date" label="Date From" />
                <flux:input wire:model.defer="date_to"   type="date" label="Date To" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model.defer="day_from" type="number" min="1" label="War Day From (optional)" />
                <flux:input wire:model.defer="day_to"   type="number" min="1" label="War Day To (optional)" />
            </div>
        </div>

        <flux:textarea
            wire:model.defer="description"
            label="Post Description"
            placeholder="Overall summary of operations and context..."
            rows="5"
        />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input
                type="number"
                step="0.01"
                min="0"
                wire:model.defer="overall_ukrainian_advance_km2"
                label="Overall Ukrainian Advance (km²)"
                placeholder="e.g., 12.50"
            />
            <div class="text-sm text-zinc-500 dark:text-zinc-400 flex items-center">
                <span>
                    Optional manual figure; leave blank to rely solely on item totals below.
                </span>
            </div>
        </div>

        {{-- Live totals --}}
        <flux:callout icon="chart-bar">
            <flux:callout.heading>Live totals (from items below)</flux:callout.heading>
            <flux:callout.text>
                <div class="text-sm mt-1">
                    Total Russian Advance (Gross):
                    <strong>{{ number_format($computed_totals['russia'] ?? 0, 2) }} km²</strong><br>
                    Total Ukrainian Advance (Gross):
                    <strong>{{ number_format($computed_totals['ukraine'] ?? 0, 2) }} km²</strong>
                </div>
            </flux:callout.text>
        </flux:callout>

        <div class="pt-2">
            <flux:button wire:click="save" icon="check">Save</flux:button>
        </div>
    </flux:card>

    {{-- Items --}}
    <flux:card class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="md">Pictures & Entries</flux:heading>
                <flux:text class="mt-1">Add one or many per post. Each can track an advance value.</flux:text>
            </div>
            <flux:button wire:click="addItem" icon="plus">Add Item</flux:button>
        </div>

        @forelse ($items as $index => $row)
            <flux:card class="p-4 space-y-4">
                <div class="flex items-start justify-between">
                    <div class="text-sm text-zinc-500">#{{ $index + 1 }}</div>
                    <div class="flex gap-2">
                        <flux:button size="xs" variant="ghost" wire:click="moveItemUp({{ $index }})" icon="arrow-up">Up</flux:button>
                        <flux:button size="xs" variant="ghost" wire:click="moveItemDown({{ $index }})" icon="arrow-down">Down</flux:button>
                        <flux:button size="xs" variant="danger" wire:click="removeItem({{ $index }})" icon="trash">Remove</flux:button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Image & Alt --}}
                    <div class="space-y-3">
                        {{-- Free (non-Pro) file input --}}
                        <flux:input
                            type="file"
                            wire:model="items.{{ $index }}.image"
                            label="Upload Image (optional)"
                        />

                        @if (!empty($row['image_path']))
                            <div class="text-xs text-zinc-500 break-all">Saved: {{ $row['image_path'] }}</div>
                            <img
                                src="{{ !empty($row['image_path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($row['image_path']) : '' }}"
                                class="rounded max-h-48"
                                alt=""
                            >
                        @endif

                        <flux:input
                            wire:model.defer="items.{{ $index }}.image_alt"
                            label="Image Alt (accessibility)"
                        />
                    </div>

                    {{-- Side + Advance --}}
                    <div class="space-y-3">
                        <flux:select wire:model.defer="items.{{ $index }}.side" label="Side">
                            <flux:select.option value="neutral">Neutral/Context</flux:select.option>
                            <flux:select.option value="russia">Russia</flux:select.option>
                            <flux:select.option value="ukraine">Ukraine</flux:select.option>
                        </flux:select>

                        <flux:input
                            type="number"
                            step="0.01"
                            min="0"
                            wire:model.defer="items.{{ $index }}.advance_km2"
                            label="Advance (km²)"
                            placeholder="e.g., 9.94"
                        />
                        <flux:text class="text-xs">Used for totals &amp; comparisons</flux:text>
                    </div>

                    {{-- Short + Long descriptions --}}
                    <div class="space-y-3">
                        <flux:input
                            wire:model.defer="items.{{ $index }}.short_description"
                            label="Short Description"
                            placeholder="e.g., Advance = 9.94km²"
                        />

                        <flux:textarea
                            wire:model.defer="items.{{ $index }}.long_description"
                            label="Long Description"
                            rows="6"
                            placeholder="Detailed narrative of movements, villages, direction, expected actions..."
                        />
                    </div>
                </div>
            </flux:card>
        @empty
            <flux:card class="p-8 text-center">
                <flux:heading size="md" class="mb-2">No items yet</flux:heading>
                <flux:text class="mb-4">Add your first map/picture entry.</flux:text>
                <flux:button wire:click="addItem" icon="plus">Add Item</flux:button>
            </flux:card>
        @endforelse

        <div class="pt-2">
            <flux:button wire:click="save" icon="check">Save</flux:button>
        </div>
    </flux:card>
</div>
