<?php

namespace App\Services;

use App\Models\Credential;
use Illuminate\Support\Facades\Auth;

class CredentialService
{
    public function getStats(): array
    {
        $userId = Auth::id();
        $base = Credential::where('user_id', $userId);

        $total = (clone $base)->count();
        $favorites = (clone $base)->where('is_favorite', true)->count();
        $expiring = (clone $base)->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->count();
        $byType = (clone $base)->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return compact('total', 'favorites', 'expiring', 'byType');
    }

    public function search(string $query)
    {
        return Credential::where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('label', 'like', "%{$query}%")
                  ->orWhere('provider', 'like', "%{$query}%")
                  ->orWhere('domain', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%");
            })
            ->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);
    }
}
