<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $service = app(ClientService::class);
        $search = $request->input('q', '');
        $clients = $service->search(search: $search, perPage: 10);
        $stats = $service->getStats();

        return view('clients.index', compact('clients', 'stats', 'search'));
    }

    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);
        auth()->user()->clients()->create($request->validated());
        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);
        $client->update($request->validated());
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
