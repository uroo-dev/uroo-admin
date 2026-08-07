<?php

namespace App\Services;

use App\Models\Commit;
use App\Models\Repository;
use Illuminate\Support\Facades\Http;

class GitHubApiService
{
    protected string $token;
    protected string $username;

    public function __construct()
    {
        $this->token = (string) (config('github.token') ?? '');
        $this->username = (string) (config('github.username') ?? '');
    }

    public function isConfigured(): bool
    {
        return $this->token !== '' && $this->username !== '';
    }

    public function syncRepositories(int $userId): int
    {
        if (!$this->isConfigured()) {
            return 0;
        }

        $page = 1;
        $count = 0;

        do {
            $response = Http::withToken($this->token)
                ->get("https://api.github.com/users/{$this->username}/repos", [
                    'page' => $page,
                    'per_page' => 100,
                    'sort' => 'updated',
                    'direction' => 'desc',
                    'type' => 'all',
                ]);

            if (!$response->successful()) {
                break;
            }

            $repos = $response->json();

            foreach ($repos as $repo) {
                Repository::updateOrCreate(
                    ['full_name' => $repo['full_name']],
                    [
                        'user_id' => $userId,
                        'name' => $repo['name'],
                        'description' => $repo['description'],
                        'url' => $repo['html_url'],
                        'language' => $repo['language'],
                        'stars' => $repo['stargazers_count'],
                        'forks' => $repo['forks_count'],
                        'open_issues' => $repo['open_issues_count'],
                        'default_branch' => $repo['default_branch'],
                        'is_private' => $repo['private'],
                        'is_archived' => $repo['archived'],
                        'last_pushed_at' => $repo['pushed_at'],
                    ]
                );
                $count++;
            }

            $page++;
        } while (count($repos) === 100);

        return $count;
    }

    public function syncCommits(int $userId, ?int $repositoryId = null): int
    {
        if (!$this->isConfigured()) {
            return 0;
        }

        $count = 0;

        Repository::where('user_id', $userId)
            ->when($repositoryId, fn ($q) => $q->where('id', $repositoryId))
            ->chunk(20, function ($repos) use (&$count) {
                foreach ($repos as $repo) {
                    $page = 1;

                    do {
                        $response = Http::withToken($this->token)
                            ->get("https://api.github.com/repos/{$repo->full_name}/commits", [
                                'page' => $page,
                                'per_page' => 100,
                                'sha' => $repo->default_branch,
                            ]);

                        if (!$response->successful()) {
                            $this->handleFailedResponse($response);
                            break;
                        }

                        $this->checkRateLimit($response);

                        $commits = $response->json();

                        foreach ($commits as $commit) {
                            $sha = $commit['sha'];
                            $author = $commit['commit']['author'];

                            Commit::updateOrCreate(
                                ['sha' => $sha],
                                [
                                    'repository_id' => $repo->id,
                                    'message' => $commit['commit']['message'],
                                    'author_name' => $author['name'],
                                    'author_email' => $author['email'],
                                    'branch' => $repo->default_branch,
                                    'committed_at' => $author['date'],
                                ]
                            );
                            $count++;
                        }

                        $page++;
                    } while (count($commits) === 100);
                }
            });

        return $count;
    }

    private function checkRateLimit($response): void
    {
        $remaining = $response->header('X-RateLimit-Remaining');

        if ($remaining !== null && (int) $remaining < 10) {
            $reset = (int) $response->header('X-RateLimit-Reset');
            $wait = max($reset - time(), 0) + 5;
            sleep($wait);
        }
    }

    private function handleFailedResponse($response): void
    {
        $remaining = $response->header('X-RateLimit-Remaining');

        if ($remaining !== null && (int) $remaining === 0) {
            $reset = (int) $response->header('X-RateLimit-Reset');
            $wait = max($reset - time(), 0) + 5;
            sleep($wait);
        }
    }

    public function syncAll(int $userId): array
    {
        $reposCount = $this->syncRepositories($userId);
        $commitsCount = $this->syncCommits($userId);

        return [
            'repositories' => $reposCount,
            'commits' => $commitsCount,
        ];
    }
}
