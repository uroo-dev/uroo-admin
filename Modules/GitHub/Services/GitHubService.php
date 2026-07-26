<?php

namespace Modules\GitHub\Services;

use Illuminate\Support\Facades\Cache;
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
        return Cache::remember('github:contributions', 3600, function () {
            $api = app(GitHubApiService::class);
            $calendar = $api->fetchContributions();

            if ($calendar !== null) {
                return $this->fromGitHubGraphQL($calendar);
            }

            return $this->fromLocalDatabase();
        });
    }

    private function fromGitHubGraphQL(array $calendar): array
    {
        $total = $calendar['totalContributions'];
        $daily = [];

        foreach ($calendar['weeks'] as $week) {
            foreach ($week['contributionDays'] as $day) {
                $daily[$day['date']] = $day['contributionCount'];
            }
        }

        $maxCount = max($daily) ?: 1;
        $weeks = $this->buildContributionGrid($daily, now()->subYear());

        return compact('total', 'daily', 'maxCount', 'weeks');
    }

    private function fromLocalDatabase(): array
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