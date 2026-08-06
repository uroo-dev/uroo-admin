<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SupabaseSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private readonly SupabaseSyncService $supabase) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $this->linkToSupabase($user, $data['password']);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Mirror the new user to Supabase so the mobile app can sign in with the
     * same account and see the same rows (best effort, non-blocking).
     */
    protected function linkToSupabase(User $user, string $password): void
    {
        if (! $this->supabase->isConfigured()) {
            return;
        }

        try {
            $uid = $this->supabase->createAuthUser($user->email, $password, $user->name);
            if (! $uid) {
                return;
            }

            // Match Supabase users.id to the MySQL id so RLS/FK line up.
            $this->supabase->upsert('users', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'supabase_uid' => $uid,
                'password' => null,
                'email_verified_at' => now()->toIso8601String(),
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ], 'supabase_uid');

            $user->forceFill(['supabase_uid' => $uid])->save();
        } catch (\Throwable $e) {
            report($e); // sync is best-effort; local account still works
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
