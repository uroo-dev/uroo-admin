<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    public function getStats(): array
    {
        $userId = Auth::id();

        return [
            'total' => Project::where('user_id', $userId)->count(),
            'development' => Project::where('user_id', $userId)->where('status', 'development')->count(),
            'testing' => Project::where('user_id', $userId)->where('status', 'testing')->count(),
            'revision' => Project::where('user_id', $userId)->where('status', 'revision')->count(),
            'completed' => Project::where('user_id', $userId)->where('status', 'completed')->count(),
            'archived' => Project::where('user_id', $userId)->where('status', 'archived')->count(),
        ];
    }

    public function search(
        ?string $search = null,
        ?string $category = null,
        ?string $status = null,
        string $sortField = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 10,
    ): LengthAwarePaginator {
        $allowedSorts = ['name', 'status', 'category', 'created_at', 'deadline', 'storage_usage'];

        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        return Project::where('user_id', Auth::id())
            ->with('client:id,name')
            ->when($search, fn ($q) => $q->whereAny([
                'name', 'description', 'category', 'platform',
            ], 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
}
