<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Services\ClientService;

class ClientList extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';
    public ?int $deleteId = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function clients(): LengthAwarePaginator
    {
        return app(ClientService::class)->search(
            search: $this->search,
            perPage: 10,
        );
    }

    #[On('delete-client')]
    public function delete(): void
    {
        if ($this->deleteId) {
            $client = Client::findOrFail($this->deleteId);
            $this->authorize('delete', $client);
            $client->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Deleted', text: 'Client has been deleted.');
        }
    }

    #[On('client-saved')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->authorize('update', $client);
        $client->update([
            'status' => $client->status === 'active' ? 'inactive' : 'active',
        ]);

        $this->dispatch('swal:success', title: 'Status Updated', text: "Client is now {$client->fresh()->status}.");
    }

    public function edit(int $id): void
    {
        $this->dispatch('open-modal', id: 'client-form');
        $this->dispatch('editClient', id: $id);
    }

    public function view(int $id): void
    {
        $this->dispatch('open-modal', id: 'client-detail');
        $this->dispatch('view-client', id: $id);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-client', 'title' => 'Hapus client?', 'confirmText' => 'Ya, hapus!']);
    }

    public function render()
    {
        $stats = app(ClientService::class)->getStats();
        return view('livewire.client-list', [
            'clients' => $this->clients,
            'stats' => $stats,
        ]);
    }
}
