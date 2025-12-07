<?php

namespace App\Livewire\Frontline;

use App\Models\City;
use App\Models\Region;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.frontline')]
#[Title('Frontline Control Dashboard')]
class ControlBoard extends Component
{
    public string $scope = 'all'; // all, cities, regions
    public string $dominance = 'any'; // any, russia, ukraine, contested
    public string $sort = 'name'; // name, ru_desc, ua_desc, updated_desc
    public string $search = '';

    protected $queryString = [
        'scope'     => ['except' => 'all'],
        'dominance' => ['except' => 'any'],
        'sort'      => ['except' => 'name'],
        'search'    => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetLocalState();
    }

    public function updatedScope(): void
    {
        $this->resetLocalState();
    }

    public function updatedDominance(): void
    {
        $this->resetLocalState();
    }

    public function updatedSort(): void
    {
        $this->resetLocalState();
    }

    public function render()
    {
        $items = $this->items();

        $summary = [
            'visible_count' => $items->count(),
            'avg_ru'        => $items->avg('ru') ? round($items->avg('ru'), 1) : 0.0,
            'avg_ua'        => $items->avg('ua') ? round($items->avg('ua'), 1) : 0.0,
        ];

        return view('livewire.frontline.control-board', [
            'items'   => $items,
            'summary' => $summary,
        ]);
    }

    private function items(): Collection
    {
        $items = collect();

        if ($this->scope !== 'regions') {
            $items = $items->merge(
                City::orderBy('name')
                    ->get()
                    ->map(fn (City $city) => $this->mapItem($city, 'city'))
            );
        }

        if ($this->scope !== 'cities') {
            $items = $items->merge(
                Region::orderBy('name')
                    ->get()
                    ->map(fn (Region $region) => $this->mapItem($region, 'region'))
            );
        }

        if ($this->search !== '') {
            $search = mb_strtolower($this->search);
            $items = $items->filter(function (array $item) use ($search) {
                return str_contains(mb_strtolower($item['name']), $search);
            });
        }

        $items = $items->filter(function (array $item) {
            return $this->matchesDominance($item);
        });

        $items = $this->applySort($items);

        return $items->values();
    }

    private function mapItem(City|Region $model, string $type): array
    {
        $ru = max(0.0, min(100.0, (float) $model->russian_control_percent));
        $ua = max(0.0, min(100.0, (float) $model->ukrainian_control_percent));
        $neutral = max(0.0, round(100.0 - ($ru + $ua), 2));

        return [
            'id'       => $model->id,
            'name'     => $model->name,
            'type'     => $type,
            'ru'       => round($ru, 2),
            'ua'       => round($ua, 2),
            'neutral'  => $neutral,
            'updated'  => $model->updated_at,
        ];
    }

    private function matchesDominance(array $item): bool
    {
        return match ($this->dominance) {
            'russia'    => $item['ru'] > $item['ua'],
            'ukraine'   => $item['ua'] > $item['ru'],
            'contested' => abs($item['ua'] - $item['ru']) <= 10 || $item['neutral'] > 0,
            default     => true,
        };
    }

    private function applySort(Collection $items): Collection
    {
        return match ($this->sort) {
            'ru_desc'       => $items->sortByDesc(fn ($item) => $item['ru'])->values(),
            'ua_desc'       => $items->sortByDesc(fn ($item) => $item['ua'])->values(),
            'neutral_desc'  => $items->sortByDesc(fn ($item) => $item['neutral'])->values(),
            'updated_desc'  => $items->sortByDesc(fn ($item) => optional($item['updated'])->timestamp ?? 0)->values(),
            default         => $items->sortBy(fn ($item) => mb_strtolower($item['name']))->values(),
        };
    }

    private function resetLocalState(): void
    {
        // placeholder for future pagination or extra state resets
    }
}
