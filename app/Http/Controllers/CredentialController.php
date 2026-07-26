<?php

namespace App\Http\Controllers;

use App\Http\Requests\CredentialRequest;
use App\Models\Credential;

class CredentialController extends Controller
{
    public function index()
    {
        return view('credentials.index');
    }

    public function store(CredentialRequest $request)
    {
        auth()->user()->credentials()->create($request->validated());

        return redirect()->route('credentials.index')->with('success', 'Credential created successfully.');
    }

    public function update(CredentialRequest $request, Credential $credential)
    {
        $this->authorize('update', $credential);
        $credential->update($request->validated());

        return redirect()->route('credentials.index')->with('success', 'Credential updated successfully.');
    }

    public function destroy(Credential $credential)
    {
        $this->authorize('delete', $credential);
        $credential->delete();

        return redirect()->route('credentials.index')->with('success', 'Credential deleted successfully.');
    }
}
