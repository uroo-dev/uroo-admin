<?php

use App\Console\Commands\SyncGitHub;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('github:sync {user_id?}', function () {
    $this->call(SyncGitHub::class, [
        'user_id' => $this->argument('user_id'),
    ]);
})->purpose('Sync repositories and commits from GitHub');
