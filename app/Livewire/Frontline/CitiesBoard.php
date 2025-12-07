<?php

namespace App\Livewire\Frontline;

use App\Models\City;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.frontline')]
#[Title('City Control Overview')]
class CitiesBoard extends Component
{
    use WithPagination;

    public int $perPage = 12;
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        // no-op; filtering happens on button click
    }

    public function applySearch(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $cities = City::query()
            ->when($this->search, function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('updated_at')
            ->orderBy('name')
            ->paginate($this->perPage)
            ->withQueryString();

        return view('livewire.frontline.cities-board', [
            'cities' => $cities,
        ]);
    }
}
