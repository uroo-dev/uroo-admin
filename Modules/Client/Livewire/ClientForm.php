<?php

namespace Modules\Client\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Client\Models\Client;
use Modules\Client\Requests\ClientRequest;

class ClientForm extends Component
{
    public ?Client $client = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $whatsapp = '';
    public string $company = '';
    public string $address = '';
    public string $website = '';
    public string $notes = '';
    public string $status = 'active';

    public bool $isEdit = false;

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->client = Client::findOrFail($id);
            $this->authorize('update', $this->client);
            $this->isEdit = true;
            $this->name = $this->client->name;
            $this->email = $this->client->email ?? '';
            $this->phone = $this->client->phone ?? '';
            $this->whatsapp = $this->client->whatsapp ?? '';
            $this->company = $this->client->company ?? '';
            $this->address = $this->client->address ?? '';
            $this->website = $this->client->website ?? '';
            $this->notes = $this->client->notes ?? '';
            $this->status = $this->client->status;
        }
    }

    public function save(): void
    {
        $validated = $this->validate((new ClientRequest)->rules());

        if ($this->isEdit) {
            $this->client->update($validated);
        } else {
            $this->client = Client::create(array_merge($validated, [
                'user_id' => auth()->id(),
            ]));
        }

        $this->dispatch('client-saved');
        $action = $this->isEdit ? 'updated' : 'created';
        $this->dispatch('swal:success', title: 'Client ' . $action, text: "Client has been {$action} successfully.");

        $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        return view('client::livewire.client-form');
    }
}
