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

    public function getContributionStats(): array
    {
        $oneYearAgo = now()->subYear();

        $total = Commit::where('committed_at', '>=', $oneYearAgo)->count();

        $daily = Commit::where('committed_at', '>=', $oneYearAgo)
            ->selectRaw('DATE(committed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $maxCount = $daily->max() ?: 1;

        $weeks = $this->buildContributionGrid($daily, $oneYearAgo);

        return compact('total', 'daily', 'maxCount', 'weeks');
    }

    private function buildContributionGrid($daily, $oneYearAgo): array
    {
        $weeks = [];
        $current = $oneYearAgo->copy()->startOfWeek();

        $end = now()->endOfWeek();

        while ($current <= $end) {
            $week = [];
            for ($day = 0; $day < 7; $day++) {
                $dateStr = $current->format('Y-m-d');
                $week[] = [
                    'date' => $dateStr,
                    'count' => $daily[$dateStr] ?? 0,
                ];
                $current->addDay();
            }
            $weeks[] = $week;
        }

        return $weeks;
    }

    public function searchRepositories(string $query)
    {
        return Repository::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('language', 'like', "%{$query}%")
            ->paginate(10);
    }
}