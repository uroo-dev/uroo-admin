<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppIdea;
use Illuminate\Support\Collection;

class IdeaService
{
    public function getStats(int $userId): array
    {
        $ideas = AppIdea::where('user_id', $userId);

        return [
            'total' => (clone $ideas)->count(),
            'draft' => (clone $ideas)->where('status', 'draft')->count(),
            'research' => (clone $ideas)->where('status', 'research')->count(),
            'development' => (clone $ideas)->where('status', 'development')->count(),
            'archived' => (clone $ideas)->where('status', 'archived')->count(),
            'high_priority' => (clone $ideas)->where('priority', 'high')->count(),
        ];
    }

    public function getByPlatform(int $userId): Collection
    {
        return AppIdea::where('user_id', $userId)
            ->selectRaw('platform, count(*) as total')
            ->groupBy('platform')
            ->pluck('total', 'platform');
    }

    public function search(int $userId, string $query): Collection
    {
        return AppIdea::where('user_id', $userId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('tagline', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}
