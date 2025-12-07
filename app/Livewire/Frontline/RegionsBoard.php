<?php

namespace App\Livewire\Frontline;

use App\Models\Region;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.frontline')]
#[Title('Regional Control Overview')]
class RegionsBoard extends Component
{
    public function render()
    {
        $regions = Region::orderBy('name')->get();

        return view('livewire.frontline.regions-board', [
            'regions' => $regions,
        ]);
    }
}
