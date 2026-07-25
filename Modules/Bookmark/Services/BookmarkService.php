<?php

namespace Modules\Bookmark\Services;

use Modules\Bookmark\Models\Bookmark;

class BookmarkService
{
    public function getStats(): array
    {
        $total = Bookmark::count();
        $favorites = Bookmark::where('is_favorite', true)->count();
        $byCategory = Bookmark::selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        return compact('total', 'favorites', 'byCategory');
    }

    public function search(string $query)
    {
        return Bookmark::where('title', 'like', "%{$query}%")
            ->orWhere('url', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(24);
    }
}
