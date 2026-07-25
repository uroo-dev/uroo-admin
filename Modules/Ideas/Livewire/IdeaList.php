<?php

declare(strict_types=1);

namespace Modules\Ideas\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Ideas\Models\AppIdea;

class IdeaList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $platformFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'platformFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function ideas(): LengthAwarePaginator
    {
        return AppIdea::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('tagline', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->platformFilter, fn ($query) => $query->where('platform', $this->platformFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(12);
    }

    public function delete(int $id): void
    {
        $idea = AppIdea::where('user_id', auth()->id())->findOrFail($id);
        $idea->delete();
    }

    public function render(): View
    {
        return view('ideas::livewire.idea-list', [
            'ideas' => $this->ideas,
        ]);
    }
}
