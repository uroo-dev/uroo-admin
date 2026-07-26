<?php

namespace Modules\GitHub\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\GitHub\Models\Repository;

class RepositoryList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $language = '';
    public string $sortField = 'last_pushed_at';
    public string $sortDirection = 'desc';

    private const ALLOWED_SORT_FIELDS = [
        'name', 'stars', 'forks', 'open_issues', 'updated_at', 'last_pushed_at',
    ];

    protected $queryString = ['search', 'language', 'sortField', 'sortDirection'];

    public function render()
    {
        $query = Repository::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->language) {
            $query->where('language', $this->language);
        }

        $sortField = in_array($this->sortField, self::ALLOWED_SORT_FIELDS, true)
            ? $this->sortField
            : 'last_pushed_at';

        $repositories = $query->orderBy($sortField, $this->sortDirection)->paginate(10);

        $languages = Cache::remember('github:languages:v2', 3600, fn () =>
            Repository::select('language')->distinct()->whereNotNull('language')->orderBy('language')->pluck('language')->values()->toArray()
        );

        return view('livewire.repository-list', compact('repositories', 'languages'));
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('sync-github')]
    public function syncFromGitHub(): void
    {
        $api = app(\Modules\GitHub\Services\GitHubApiService::class);

        if (!$api->isConfigured()) {
            $this->dispatch('swal:error', title: 'GitHub not configured', text: 'Set GITHUB_TOKEN and GITHUB_USERNAME in .env');
            return;
        }

        $result = $api->syncAll(auth()->id());
        Cache::forget('github:languages:v2');
        Cache::forget('github:contributions');
        $this->dispatch('swal:success', title: 'Sync completed', text: "{$result['repositories']} repos, {$result['commits']} commits synced");
        $this->resetPage();
    }
}