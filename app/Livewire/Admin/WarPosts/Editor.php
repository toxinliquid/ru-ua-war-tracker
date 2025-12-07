<?php

namespace App\Livewire\Admin\WarPosts;

use App\Models\WarPost;
use App\Models\WarPostItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use WithFileUploads;

    public ?WarPost $post = null;

    // Top-level fields
    #[Validate('nullable|string|max:255')] public $title = '';
    #[Validate('required|date')]           public $date_from;
    #[Validate('required|date|after_or_equal:date_from')] public $date_to;
    #[Validate('nullable|integer|min:1')]  public $day_from;
    #[Validate('nullable|integer|min:1')]  public $day_to;
    #[Validate('nullable|string')]         public $description;
    #[Validate('nullable|numeric|min:0|max:1000000')] public $overall_ukrainian_advance_km2;

    // Items (dynamic list)
    public array $items = []; // each item: [id?, image (tmp), image_path?, image_alt, side, short_description, long_description, advance_km2, position]

    public function mount(?int $postId = null): void
    {
        if ($postId) {
            $this->post = WarPost::with('items')->findOrFail($postId);

            $this->fill([
                'title'       => $this->post->title,
                'date_from'   => optional($this->post->date_from)->format('Y-m-d'),
                'date_to'     => optional($this->post->date_to)->format('Y-m-d'),
                'day_from'    => $this->post->day_from,
                'day_to'      => $this->post->day_to,
                'description' => $this->post->description,
                'overall_ukrainian_advance_km2' => $this->post->overall_ukrainian_advance_km2,
                'items'       => $this->post->items->map(function (WarPostItem $i) {
                    return [
                        'id'                => $i->id,
                        'image'             => null, // upload placeholder
                        'image_path'        => $i->image_path,
                        'image_alt'         => $i->image_alt,
                        'side'              => $i->side,
                        'short_description' => $i->short_description,
                        'long_description'  => $i->long_description,
                        'advance_km2'       => $i->advance_km2,
                        'position'          => $i->position,
                    ];
                })->values()->toArray(),
            ]);
        } else {
            // sensible defaults
            $this->date_from = now()->toDateString();
            $this->date_to   = now()->toDateString();
            $this->items     = [];
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'id'                => null,
            'image'             => null,
            'image_path'        => null,
            'image_alt'         => null,
            'side'              => 'neutral',
            'short_description' => null,
            'long_description'  => null,
            'advance_km2'       => null,
            'position'          => count($this->items),
        ];
    }

    public function removeItem(int $index): void
    {
        if (! isset($this->items[$index])) return;
        // If it has an existing DB record, we’ll delete later after save, but for UX we drop it here.
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->reindexPositions();
    }

    public function moveItemUp(int $index): void
    {
        if ($index <= 0 || !isset($this->items[$index])) return;
        [$this->items[$index-1], $this->items[$index]] = [$this->items[$index], $this->items[$index-1]];
        $this->reindexPositions();
    }

    public function moveItemDown(int $index): void
    {
        if (!isset($this->items[$index]) || $index >= count($this->items)-1) return;
        [$this->items[$index+1], $this->items[$index]] = [$this->items[$index], $this->items[$index+1]];
        $this->reindexPositions();
    }

    protected function reindexPositions(): void
    {
        foreach ($this->items as $i => &$row) {
            $row['position'] = $i;
        }
    }

    public function save()
    {
        $this->validate();

        $isNew = $this->post === null;

        // Per-item validation
        foreach ($this->items as $i => $row) {
            $this->validate([
                "items.$i.side"              => 'required|in:russia,ukraine,neutral',
                "items.$i.short_description" => 'nullable|string|max:255',
                "items.$i.long_description"  => 'nullable|string',
                "items.$i.advance_km2"       => 'nullable|numeric|min:0|max:1000000',
                "items.$i.image"             => 'nullable|image|max:6144', // 6MB
                "items.$i.image_alt"         => 'nullable|string|max:255',
            ]);
        }

        DB::transaction(function () {
            // Create or update the post
            $post = $this->post ?? new WarPost();
            $post->fill([
                'title'       => $this->title ?: null,
                'date_from'   => $this->date_from,
                'date_to'     => $this->date_to,
                'day_from'    => $this->day_from ?: null,
                'day_to'      => $this->day_to ?: null,
                'description' => $this->description ?: null,
                'overall_ukrainian_advance_km2' => $this->overall_ukrainian_advance_km2 === ''
                    ? null
                    : $this->overall_ukrainian_advance_km2,
            ])->save();

            // Track existing item ids to keep/delete
            $existingIds = $post->items()->pluck('id')->all();
            $keepIds     = [];

            foreach ($this->items as $i => $row) {
                // Handle upload if present
                $imagePath = $row['image_path'] ?? null;
                if (!empty($row['image'])) {
                    $imagePath = $row['image']->store('war_posts', 'public');
                }

                $item = null;
                if (!empty($row['id'])) {
                    $item = WarPostItem::where('war_post_id', $post->id)->find($row['id']);
                }
                if (! $item) {
                    $item = new WarPostItem(['war_post_id' => $post->id]);
                }

                $item->fill([
                    'image_path'        => $imagePath,
                    'image_alt'         => Arr::get($row, 'image_alt'),
                    'side'              => Arr::get($row, 'side', 'neutral'),
                    'short_description' => Arr::get($row, 'short_description'),
                    'long_description'  => Arr::get($row, 'long_description'),
                    'advance_km2'       => Arr::get($row, 'advance_km2'),
                    'position'          => (int) Arr::get($row, 'position', $i),
                ])->save();

                $this->items[$i]['id']         = $item->id;
                $this->items[$i]['image_path'] = $item->image_path;

                $keepIds[] = $item->id;
            }

            // Delete removed items from DB and storage
            $toDelete = array_diff($existingIds, $keepIds);
            if ($toDelete) {
                $items = WarPostItem::whereIn('id', $toDelete)->get();
                foreach ($items as $del) {
                    if ($del->image_path && Storage::disk('public')->exists($del->image_path)) {
                        Storage::disk('public')->delete($del->image_path);
                    }
                    $del->delete();
                }
            }

            // Refresh cached totals
            $post->refreshCachedTotals();

            $this->post = $post; // make sure future saves use same record
        });

        $this->dispatch('ok', message: 'Saved successfully.');

        if ($isNew) {
            return $this->redirectRoute('admin.war-posts.index');
        }
    }

    public function render()
    {
        // Compute live totals for preview
        $ru = 0.0; $ua = 0.0;
        foreach ($this->items as $row) {
            $val = (float) ($row['advance_km2'] ?? 0);
            if (($row['side'] ?? '') === 'russia')  $ru += $val;
            if (($row['side'] ?? '') === 'ukraine') $ua += $val;
        }

        return view('livewire.admin.war-posts.editor', [
            'computed_totals' => [
                'russia'  => round($ru, 2),
                'ukraine' => round($ua, 2),
            ],
        ]);
    }
}
