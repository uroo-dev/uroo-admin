<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        return view('clients.index');
    }

    public function store(ClientRequest $request)
    {
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
