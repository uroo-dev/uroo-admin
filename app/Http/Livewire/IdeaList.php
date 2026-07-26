<?php

namespace App\Http\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AppIdea;

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
    public function ideas()
    {
        $allowedSorts = ['name', 'status', 'priority', 'platform', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSorts) ? $this->sortField : 'created_at';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return AppIdea::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('tagline', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->platformFilter, fn ($q) => $q->where('platform', $this->platformFilter))
            ->orderBy($sortField, $sortDirection)
            ->paginate(12);
    }

    public function delete(int $id): void
    {
        AppIdea::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Idea berhasil dihapus');
    }

    public function render()
    {
        $stats = [
            'total' => AppIdea::where('user_id', auth()->id())->count(),
            'draft' => AppIdea::where('user_id', auth()->id())->where('status', 'draft')->count(),
            'development' => AppIdea::where('user_id', auth()->id())->where('status', 'development')->count(),
            'archived' => AppIdea::where('user_id', auth()->id())->where('status', 'archived')->count(),
        ];

        return view('livewire.idea-list', [
            'ideas' => $this->ideas,
            'stats' => $stats,
        ]);
    }
}
