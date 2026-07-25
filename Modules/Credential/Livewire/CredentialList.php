<?php

namespace Modules\Credential\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Credential\Models\Credential;
use Modules\Credential\Services\CredentialService;

class CredentialList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public bool $showFavorites = false;

    protected $queryString = ['search', 'type', 'showFavorites'];

    public function render()
    {
        $query = Credential::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', "%{$this->search}%")
                  ->orWhere('provider', 'like', "%{$this->search}%")
                  ->orWhere('domain', 'like', "%{$this->search}%");
            });
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->showFavorites) {
            $query->where('is_favorite', true);
        }

        $credentials = $query->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        $stats = app(CredentialService::class)->getStats();
        $types = Credential::types();

        return view('credentials.index', compact('credentials', 'stats', 'types'));
    }

    public function toggleFavorite(int $id): void
    {
        $credential = Credential::findOrFail($id);
        $credential->update(['is_favorite' => !$credential->is_favorite]);
    }

    public function deleteCredential(int $id): void
    {
        Credential::findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Credential berhasil dihapus');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}