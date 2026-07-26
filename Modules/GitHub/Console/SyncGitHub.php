<?php

namespace Modules\GitHub\Console;

use Illuminate\Console\Command;
use Modules\GitHub\Services\GitHubApiService;

class SyncGitHub extends Command
{
    protected $signature = 'github:sync {user_id?}';
    protected $description = 'Sync repositories and commits from GitHub';

    public function handle(GitHubApiService $api): int
    {
        $userId = $this->argument('user_id') ?? 1;

        if (!$api->isConfigured()) {
            $this->error('GITHUB_TOKEN and GITHUB_USERNAME must be set in .env');
            return Command::FAILURE;
        }

        $this->info('Syncing from GitHub...');
        $result = $api->syncAll((int) $userId);

        $this->info("Synced {$result['repositories']} repositories and {$result['commits']} commits.");

        return Command::SUCCESS;
    }
}
