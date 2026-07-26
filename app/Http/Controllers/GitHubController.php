<?php

namespace App\Http\Controllers;

use App\Models\Commit;
use App\Models\Repository;
use App\Services\GitHubApiService;
use App\Services\GitHubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GitHubController extends Controller
{
    public function index(Request $request)
    {
        $api = app(GitHubApiService::class);
        if (!Repository::exists() && $api->isConfigured()) {
            $api->syncAll(auth()->id());
            Cache::forget('github:languages:v2');
        }

        $stats = app(GitHubService::class)->getStats();

        $search = $request->input('search', '');
        $language = $request->input('language', '');
        $allowedSortFields = ['name', 'stars', 'forks', 'open_issues', 'updated_at', 'last_pushed_at'];
        $sortField = $request->input('sort', 'last_pushed_at');
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'last_pushed_at';
        }
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $repoQuery = Repository::query();
        if ($search) {
            $repoQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($language) {
            $repoQuery->where('language', $language);
        }
        $repositories = $repoQuery->orderBy($sortField, $sortDirection)->paginate(10)->appends($request->query());

        $languages = Cache::remember('github:languages:v2', 3600, fn () =>
            Repository::select('language')->distinct()->whereNotNull('language')->orderBy('language')->pluck('language')->values()->toArray()
        );

        $commitRepoId = $request->input('repository_id');
        $commitQuery = Commit::with('repository:id,name');
        if ($commitRepoId) {
            $commitQuery->where('repository_id', $commitRepoId);
        }
        $commits = $commitQuery->orderBy('committed_at', 'desc')->paginate(15, ['*'], 'commits_page')->appends($request->query());
        $commitRepos = Repository::select('id', 'name')->orderBy('name')->get();

        return view('github.index', compact(
            'stats', 'repositories', 'languages', 'search', 'language', 'sortField', 'sortDirection',
            'commits', 'commitRepoId', 'commitRepos'
        ));
    }

    public function sync(Request $request)
    {
        $api = app(GitHubApiService::class);
        if (!$api->isConfigured()) {
            return redirect()->route('github')->with('error', 'GitHub not configured. Set GITHUB_TOKEN and GITHUB_USERNAME in .env');
        }
        $result = $api->syncAll(auth()->id());
        Cache::forget('github:languages:v2');
        Cache::forget('github:contributions');
        return redirect()->route('github')->with('success', "Synced {$result['repositories']} repos, {$result['commits']} commits");
    }
}
