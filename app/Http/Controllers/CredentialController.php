<?php

namespace App\Http\Controllers;

use App\Http\Requests\CredentialRequest;
use App\Models\Credential;
use App\Services\CredentialService;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $query = Credential::query();
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $showFavorites = $request->boolean('favorites');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
                  ->orWhere('domain', 'like', "%{$search}%");
            });
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($showFavorites) {
            $query->where('is_favorite', true);
        }

        $credentials = $query->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        $stats = app(CredentialService::class)->getStats();
        $types = Credential::types();

        return view('credentials.index', compact('credentials', 'stats', 'types', 'search', 'type', 'showFavorites'));
    }

    public function store(CredentialRequest $request)
    {
        auth()->user()->credentials()->create($request->validated());
        return redirect()->route('credentials.index')->with('success', 'Credential created successfully.');
    }

    public function update(CredentialRequest $request, Credential $credential)
    {
        $this->authorize('update', $credential);
        $data = $request->validated();

        // Don't overwrite existing password if field left blank
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $credential->update($data);
        return redirect()->route('credentials.index')->with('success', 'Credential updated successfully.');
    }

    public function destroy(Credential $credential)
    {
        $this->authorize('delete', $credential);
        $credential->delete();
        return redirect()->route('credentials.index')->with('success', 'Credential deleted successfully.');
    }
}
