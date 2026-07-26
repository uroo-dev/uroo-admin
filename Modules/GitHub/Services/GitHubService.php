<?php

namespace Modules\GitHub\Services;

use Modules\GitHub\Models\Commit;
use Modules\GitHub\Models\Repository;

class GitHubService
{
    public function getStats(): array
    {
        $repos = Repository::count();
        $commitsToday = Commit::whereDate('committed_at', today())->count();
        $openIssues = Repository::sum('open_issues');
        $branches = Commit::distinct('branch')->count('branch');

        return compact('repos', 'commitsToday', 'openIssues', 'branches');
    }

    public function searchRepositories(string $query)
    {
        return Repository::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('language', 'like', "%{$query}%")
            ->paginate(10);
    }
}