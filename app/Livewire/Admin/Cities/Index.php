<?php

namespace App\Livewire\Admin\Cities;

use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|numeric|min:0|max:100')]
    public $russian_control_percent = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public $ukrainian_control_percent = null;

    public ?int $editingId = null;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
    }

    public function edit(int $cityId): void
    {
        $city = City::findOrFail($cityId);

        $this->editingId = $city->id;
        $this->name      = $city->name;
        $this->russian_control_percent   = $city->russian_control_percent;
        $this->ukrainian_control_percent = $city->ukrainian_control_percent;
    }

    public function delete(int $cityId): void
    {
        $city = City::findOrFail($cityId);
        $city->delete();

        $this->resetForm();
        $this->dispatch('ok', message: __('City deleted.'));
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate();

        $computed = $this->computePercentages();

        City::updateOrCreate(
            ['id' => $this->editingId],
            [
                'name'                     => $this->name,
                'russian_control_percent'  => $computed['russia'],
                'ukrainian_control_percent'=> $computed['ukraine'],
            ],
        );

        $this->dispatch('ok', message: $this->editingId ? __('City updated.') : __('City added.'));

        $this->resetForm();
        $this->resetPage();
    }

    public function render()
    {
        $cities = City::query()
            ->when($this->search, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.admin.cities.index', [
            'cities' => $cities,
        ]);
    }

    private function computePercentages(): array
    {
        $ru = $this->nullOrFloat($this->russian_control_percent);
        $ua = $this->nullOrFloat($this->ukrainian_control_percent);

        if ($ru === null && $ua === null) {
            throw ValidationException::withMessages([
                'russian_control_percent' => __('Provide at least one control percentage.'),
            ]);
        }

        if ($ru === null && $ua !== null) {
            $ru = round(100 - $ua, 2);
        } elseif ($ua === null && $ru !== null) {
            $ua = round(100 - $ru, 2);
        }

        if ($ru < 0 || $ru > 100 || $ua < 0 || $ua > 100) {
            throw ValidationException::withMessages([
                'russian_control_percent' => __('Percentages must be between 0 and 100.'),
            ]);
        }

        if (round($ru + $ua, 2) > 100) {
            throw ValidationException::withMessages([
                'russian_control_percent' => __('Combined control cannot exceed 100%.'),
            ]);
        }

        // Even if they don't sum to 100 (e.g. missing territory), we keep as is.
        $ru = round($ru, 2);
        $ua = round($ua, 2);

        return [
            'russia'  => $ru,
            'ukraine' => $ua,
        ];
    }

    private function resetForm(): void
    {
        $this->reset('editingId', 'name', 'russian_control_percent', 'ukrainian_control_percent');
    }

    private function nullOrFloat($value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (float) $value;
    }
}
