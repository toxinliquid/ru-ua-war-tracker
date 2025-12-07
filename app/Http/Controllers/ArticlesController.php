<?php

namespace App\Http\Controllers;

use App\Models\WestNews;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticlesController extends Controller
{
    public function __invoke(Request $request): View
    {
        $tone   = $request->query('tone');
        $search = $request->query('search');

        $articles = WestNews::query()
            ->when($tone, fn ($q, $tone) => $q->where('link_type', $tone))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('url', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('articles.index', [
            'articles' => $articles,
            'tone'     => $tone,
            'search'   => $search,
            'types'    => WestNews::LINK_TYPES,
        ]);
    }
}
