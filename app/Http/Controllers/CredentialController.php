<?php

namespace App\Http\Controllers;

use App\Http\Requests\CredentialRequest;
use App\Models\Credential;
use App\Services\CredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $query = Credential::where('user_id', auth()->id());
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $showFavorites = $request->boolean('favorites');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
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
        $data = $request->validated();

        // If no password provided, generate a strong one
        $generatedPassword = null;
        if (empty($data['password'])) {
            $generatedPassword = Str::random(24);
            $data['password'] = $generatedPassword;
        }

        // Create credential — use explicit assignment to trigger the password mutator
        // because 'password' is not in $fillable (only 'password_encrypted' is)
        $credential = new Credential();
        $credential->user_id     = auth()->id();
        $credential->label       = $data['label'];
        $credential->type        = $data['type'];
        $credential->username    = $data['username'] ?? null;
        $credential->password    = $data['password']; // triggers setPasswordAttribute
        $credential->is_favorite = $data['is_favorite'] ?? false;
        $credential->save();

        $msg = 'Credential berhasil disimpan.';
        if ($generatedPassword) {
            $msg = 'Credential berhasil disimpan. Password yang di-generate: ' . $generatedPassword . ' — Simpan segera!';
        }

        return redirect()->route('credentials.index')->with('success', $msg);
    }

    public function update(CredentialRequest $request, Credential $credential)
    {
        $this->authorize('update', $credential);
        $data = $request->validated();

        $credential->label    = $data['label'];
        $credential->type     = $data['type'];
        $credential->username = $data['username'] ?? null;

        // Only update password if provided
        if (!empty($data['password'])) {
            $credential->password = $data['password']; // triggers setPasswordAttribute
        }

        if (isset($data['is_favorite'])) {
            $credential->is_favorite = $data['is_favorite'];
        }

        $credential->save();

        return redirect()->route('credentials.index')->with('success', 'Credential berhasil diperbarui.');
    }

    public function destroy(Credential $credential)
    {
        $this->authorize('delete', $credential);
        $credential->delete();

        return redirect()->route('credentials.index')->with('success', 'Credential berhasil dihapus.');
    }
}
