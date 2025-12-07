<?php

namespace App\Livewire\Admin\WestNews;

use App\Models\WestNews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $title = '';

    public string $link_type = 'humiliated';

    public string $url = '';

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

    public function edit(int $westNewsId): void
    {
        $entry = WestNews::findOrFail($westNewsId);

        $this->editingId = $entry->id;
        $this->title     = $entry->title;
        $this->link_type = $entry->link_type;
        $this->url       = $entry->url;
    }

    public function delete(int $westNewsId): void
    {
        $entry = WestNews::findOrFail($westNewsId);
        $entry->delete();

        $this->resetForm();
        $this->dispatch('ok', message: __('Entry deleted.'));
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate();

        WestNews::updateOrCreate(
            ['id' => $this->editingId],
            [
                'title'     => $this->title,
                'link_type' => $this->link_type,
                'url'       => $this->url,
            ],
        );

        $this->dispatch('ok', message: $this->editingId ? __('Entry updated.') : __('Entry added.'));

        $this->resetForm();
        $this->resetPage();
    }

    protected function rules(): array
    {
        return [
            'title'     => ['required', 'string', 'max:255'],
            'link_type' => ['required', 'in:humiliated,spinning,reeling,sick,dead,weaponized,russia_collapsing,russia_invade_europe'],
            'url'       => [
                'required',
                'url',
                'max:2048',
                Rule::unique('west_news', 'url')->ignore($this->editingId),
            ],
        ];
    }

    public function render()
    {
        $entries = WestNews::query()
            ->when($this->search, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.admin.west-news.index', [
            'entries' => $entries,
            'types'   => WestNews::LINK_TYPES,
        ]);
    }

    private function resetForm(): void
    {
        $this->reset('editingId', 'title', 'link_type', 'url');
        $this->link_type = 'humiliated';
    }
}
