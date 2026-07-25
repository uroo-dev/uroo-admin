<?php

declare(strict_types=1);

namespace Modules\BrainDump\Services;

use Illuminate\Support\Collection;
use Modules\BrainDump\Models\BrainDump;

class BrainDumpService
{
    public function getActiveDumps(int $userId): Collection
    {
        return BrainDump::where('user_id', $userId)
            ->active()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getArchivedDumps(int $userId): Collection
    {
        return BrainDump::where('user_id', $userId)
            ->where('is_archived', true)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getPinnedDumps(int $userId): Collection
    {
        return BrainDump::where('user_id', $userId)
            ->active()
            ->pinned()
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getStats(int $userId): array
    {
        return [
            'total' => BrainDump::where('user_id', $userId)->count(),
            'active' => BrainDump::where('user_id', $userId)->active()->count(),
            'archived' => BrainDump::where('user_id', $userId)->where('is_archived', true)->count(),
            'pinned' => BrainDump::where('user_id', $userId)->active()->pinned()->count(),
        ];
    }

    public function search(int $userId, string $query): Collection
    {
        return BrainDump::where('user_id', $userId)
            ->active()
            ->where('content', 'like', "%{$query}%")
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}
