<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Commit;
use App\Models\Repository;

class CommitTimeline extends Component
{
    use WithPagination;

    public ?int $repositoryId = null;
    public string $branch = '';

    public function mount(?int $repositoryId = null): void
    {
        $this->repositoryId = $repositoryId;
    }

    public function render()
    {
        $query = Commit::with('repository:id,name');

        if ($this->repositoryId) {
            $query->where('repository_id', $this->repositoryId);
        }

        if ($this->branch) {
            $query->where('branch', $this->branch);
        }

        $commits = $query->orderBy('committed_at', 'desc')->paginate(15);
        $repositories = Repository::select('id', 'name')->orderBy('name')->get();

        return view('github.commits', compact('commits', 'repositories'));
    }
}
