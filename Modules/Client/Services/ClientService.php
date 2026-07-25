<?php

namespace Modules\Client\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Models\Client;

class ClientService
{
    public function getStats(): array
    {
        $userId = Auth::id();

        return [
            'total' => Client::where('user_id', $userId)->count(),
            'active' => Client::where('user_id', $userId)->where('status', 'active')->count(),
            'inactive' => Client::where('user_id', $userId)->where('status', 'inactive')->count(),
        ];
    }

    public function search(?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Client::where('user_id', Auth::id())
            ->when($search, fn ($q) => $q->whereAny([
                'name', 'email', 'phone', 'company',
            ], 'like', "%{$search}%"))
            ->latest()
            ->paginate($perPage);
    }
}
