<?php

namespace Modules\Notes\Services;

use Modules\Notes\Models\Note;

class NoteService
{
    public function getStats(): array
    {
        $total = Note::count();
        $pinned = Note::where('is_pinned', true)->count();
        $favorites = Note::where('is_favorite', true)->count();
        $byCategory = Note::selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        return compact('total', 'pinned', 'favorites', 'byCategory');
    }

    public function search(string $query)
    {
        return Note::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    }
}
