<?php

namespace App\Providers;

use App\Observers\SupabaseObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.tailwind');

        // Keep Supabase (mobile app) in sync with web writes.
        foreach (SupabaseObserver::MODELS as $model) {
            $model::observe(SupabaseObserver::class);
        }
    }
}
