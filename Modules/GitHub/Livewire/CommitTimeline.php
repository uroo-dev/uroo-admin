<?php

namespace Modules\GitHub\Livewire;

use Livewire\Component;
use Modules\GitHub\Models\Commit;
use Modules\GitHub\Models\Repository;

class CommitTimeline extends Component
{
    public ?int $repositoryId = null;
    public string $branch = '';

    public function mount(?int $repositoryId = null): void
    {
        $this->repositoryId = $repositoryId;
    }

    public function render()
    {
        $query = Commit::with('repository');

        if ($this->repositoryId) {
            $query->where('repository_id', $this->repositoryId);
        }

        if ($this->branch) {
            $query->where('branch', $this->branch);
        }

        $commits = $query->orderBy('committed_at', 'desc')->paginate(15);
        $repositories = Repository::all();

        return view('github.commits', compact('commits', 'repositories'));
    }
}