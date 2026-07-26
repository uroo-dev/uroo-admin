<?php

namespace App\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Credential;
use App\Services\CredentialService;

class CredentialList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $type = '';
    public bool $showFavorites = false;

    public ?int $editId = null;
    public ?int $deleteId = null;
    public string $label = '';
    public string $credentialType = '';
    public string $provider = '';
    public string $domain = '';
    public string $username = '';
    public string $password = '';
    public string $notes = '';

    protected $queryString = ['search', 'type', 'showFavorites'];

    protected function rules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'credentialType' => 'required|string|max:50',
            'provider' => 'nullable|string|max:255',
            'domain' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'label' => $this->label,
            'type' => $this->credentialType,
            'provider' => $this->provider,
            'domain' => $this->domain,
            'username' => $this->username,
            'password' => encrypt($this->password),
            'notes' => $this->notes,
        ];

        if ($this->editId) {
            Credential::findOrFail($this->editId)->update($data);
            $this->dispatch('swal:success', title: 'Credential diperbarui');
        } else {
            Credential::create($data);
            $this->dispatch('swal:success', title: 'Credential ditambahkan');
        }

        $this->resetForm();
        $this->dispatch('close-modal', id: 'credential-form');
    }

    public function editCredential(int $id): void
    {
        $cred = Credential::findOrFail($id);
        $this->editId = $cred->id;
        $this->label = $cred->label;
        $this->credentialType = $cred->type;
        $this->provider = $cred->provider ?? '';
        $this->domain = $cred->domain ?? '';
        $this->username = $cred->username ?? '';
        $this->password = '';
        $this->notes = $cred->notes ?? '';
        $this->dispatch('open-modal', id: 'credential-form');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-credential', 'title' => 'Hapus credential?', 'confirmText' => 'Ya, hapus!']);
    }

    #[On('delete-credential')]
    public function deleteCredential(): void
    {
        if ($this->deleteId) {
            Credential::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Credential berhasil dihapus');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editId', 'label', 'credentialType', 'provider', 'domain', 'username', 'password', 'notes']);
    }

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

        return view('livewire.credential-list', compact('credentials', 'stats', 'types'));
    }

    public function toggleFavorite(int $id): void
    {
        $credential = Credential::findOrFail($id);
        $credential->update(['is_favorite' => !$credential->is_favorite]);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
