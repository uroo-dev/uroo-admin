<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Livewire::component('repository-list', \Modules\GitHub\Livewire\RepositoryList::class);
        Livewire::component('commit-timeline', \Modules\GitHub\Livewire\CommitTimeline::class);
    }
}