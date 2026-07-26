<?php

namespace Modules\Notes\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Notes\Models\Note;

class NoteService
{
    public function getStats(): array
    {
        $userId = Auth::id();
        $base = Note::where('user_id', $userId);

        $total = (clone $base)->count();
        $pinned = (clone $base)->where('is_pinned', true)->count();
        $favorites = (clone $base)->where('is_favorite', true)->count();
        $byCategory = (clone $base)->selectRaw('category, count(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('count', 'category');

        return compact('total', 'pinned', 'favorites', 'byCategory');
    }

    public function search(string $query)
    {
        return Note::where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    }
}