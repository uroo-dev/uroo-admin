<?php

namespace App\Services;

use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;

class BookmarkService
{
    public function getStats(): array
    {
        $userId = Auth::id();
        $base = Bookmark::where('user_id', $userId);

        $total = (clone $base)->count();
        $favorites = (clone $base)->where('is_favorite', true)->count();
        $byCategory = (clone $base)->selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        return compact('total', 'favorites', 'byCategory');
    }

    public function search(string $query)
    {
        return Bookmark::where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('url', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(24);
    }
}
