<?php

namespace Modules\Credential\Services;

use Modules\Credential\Models\Credential;

class CredentialService
{
    public function getStats(): array
    {
        $total = Credential::count();
        $favorites = Credential::where('is_favorite', true)->count();
        $expiring = Credential::whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(30))
            ->count();
        $byType = Credential::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return compact('total', 'favorites', 'expiring', 'byType');
    }

    public function search(string $query)
    {
        return Credential::where('label', 'like', "%{$query}%")
            ->orWhere('provider', 'like', "%{$query}%")
            ->orWhere('domain', 'like', "%{$query}%")
            ->orWhere('username', 'like', "%{$query}%")
            ->paginate(10);
    }
}