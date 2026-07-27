<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function getStats(): array
    {
        $userId = Auth::id();

        return [
            'total' => Client::where('user_id', $userId)->count(),
            'deal' => Client::where('user_id', $userId)->where('status', 'deal')->count(),
            'pending' => Client::where('user_id', $userId)->where('status', 'pending')->count(),
            'canceled' => Client::where('user_id', $userId)->where('status', 'canceled')->count(),
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
