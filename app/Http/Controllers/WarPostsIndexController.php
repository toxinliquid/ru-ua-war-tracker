<?php

namespace App\Http\Controllers;

use App\Models\WarPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarPostsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search    = $request->query('search');
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');
        $dayFrom   = $request->query('day_from');
        $dayTo     = $request->query('day_to');

        $posts = WarPost::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom, fn ($q) => $q->whereDate('date_from', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('date_to', '<=', $dateTo))
            ->when($dayFrom, fn ($q) => $q->where('day_from', '>=', $dayFrom))
            ->when($dayTo, fn ($q) => $q->where('day_to', '<=', $dayTo))
            ->orderByDesc('date_to')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('war-posts.index', [
            'posts'    => $posts,
            'search'   => $search,
            'dateFrom' => $dateFrom,
            'dateTo'   => $dateTo,
            'dayFrom'  => $dayFrom,
            'dayTo'    => $dayTo,
        ]);
    }
}
