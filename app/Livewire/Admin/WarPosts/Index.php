<?php

namespace App\Livewire\Admin\WarPosts;

use App\Models\WarPost;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $posts = WarPost::query()
            ->when($this->search, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('date_from', 'like', "%{$search}%")
                        ->orWhere('date_to', 'like', "%{$search}%");
                });
            })
            ->withCount('items')
            ->orderByDesc('date_from')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('livewire.admin.war-posts.index', [
            'posts' => $posts,
        ]);
    }
}
