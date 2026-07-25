<?php

namespace Modules\Projects\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Projects\Models\Project;
use Modules\Projects\Services\ProjectService;

class ProjectList extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $category = '';

    #[Url(history: true)]
    public string $statusFilter = '';

    #[Url(history: true)]
    public string $sortField = 'created_at';

    #[Url(history: true)]
    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function projects(): LengthAwarePaginator
    {
        return app(ProjectService::class)->search(
            search: $this->search,
            category: $this->category,
            status: $this->statusFilter,
            sortField: $this->sortField,
            sortDirection: $this->sortDirection,
            perPage: 10,
        );
    }

    #[Computed]
    public function stats(): array
    {
        return app(ProjectService::class)->getStats();
    }

    public function delete(int $id): void
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();

        $this->dispatch('swal:success', title: 'Deleted', text: 'Project has been deleted.');
    }

    public function render()
    {
        return view('projects.index', [
            'stats' => $this->stats,
            'projects' => $this->projects,
        ]);
    }
}