<?php

namespace Modules\GitHub\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\GitHub\Models\Repository;

class RepositoryList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $language = '';
    public string $sortField = 'updated_at';
    public string $sortDirection = 'desc';

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

        $repositories = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
        $languages = Repository::select('language')->distinct()->whereNotNull('language')->pluck('language');

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
}