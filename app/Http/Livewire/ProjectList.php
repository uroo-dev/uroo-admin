<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\Project;
use App\Services\ProjectService;

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

    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $name = '';
    public string $description = '';
    public ?int $client_id = null;
    public string $categoryField = '';
    public string $statusField = 'development';
    public int $progress = 0;
    public array $techStack = [];
    public string $newTech = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'categoryField' => 'nullable|string|max:50',
            'statusField' => 'required|in:development,testing,revision,completed,archived',
            'progress' => 'required|integer|min:0|max:100',
            'techStack' => 'nullable|array',
        ];
    }

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

    public function edit(int $id): void
    {
        $project = Project::findOrFail($id);
        $this->editId = $project->id;
        $this->name = $project->name;
        $this->description = $project->description ?? '';
        $this->client_id = $project->client_id;
        $this->categoryField = $project->category ?? '';
        $this->statusField = $project->status;
        $this->progress = $project->progress ?? 0;
        $this->techStack = $project->tech_stack ?? [];
        $this->dispatch('open-modal', id: 'project-form');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
            'client_id' => $this->client_id ?: null,
            'category' => $this->categoryField,
            'status' => $this->statusField,
            'progress' => $this->progress,
            'tech_stack' => $this->techStack,
        ];

        if ($this->editId) {
            Project::findOrFail($this->editId)->update($data);
            $this->dispatch('swal:success', title: 'Project diperbarui');
        } else {
            Project::create($data);
            $this->dispatch('swal:success', title: 'Project dibuat');
        }

        $this->resetForm();
        $this->dispatch('close-modal', id: 'project-form');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-project', 'title' => 'Hapus project?', 'confirmText' => 'Ya, hapus!']);
    }

    #[On('delete-project')]
    public function delete(): void
    {
        if ($this->deleteId) {
            Project::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Project dihapus');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editId', 'name', 'description', 'client_id', 'categoryField', 'statusField', 'progress', 'techStack', 'newTech']);
    }

    public function addTech(?string $tech = null): void
    {
        $tech = trim($tech ?? $this->newTech);
        if ($tech && !in_array($tech, $this->techStack)) {
            $this->techStack[] = $tech;
        }
        $this->newTech = '';
    }

    public function removeTech(int $index): void
    {
        unset($this->techStack[$index]);
        $this->techStack = array_values($this->techStack);
    }

    public function render()
    {
        $clients = Client::select('id', 'name')->orderBy('name')->get();
        return view('livewire.project-list', [
            'stats' => $this->stats,
            'projects' => $this->projects,
            'clients' => $clients,
        ]);
    }
}
