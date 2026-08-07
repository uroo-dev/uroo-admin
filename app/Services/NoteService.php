<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Support\Facades\Auth;

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

    public function parseTags(string $tags): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $tags)),
            fn (string $tag) => $tag !== ''
        ));
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
