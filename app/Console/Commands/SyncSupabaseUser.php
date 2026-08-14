<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SupabaseSyncService;
use Illuminate\Console\Command;

/**
 * Ensures a local user has a working Supabase Auth account with a known
 * password so the Flutter mobile app can sign in with the same credentials.
 *
 *  php artisan supabase:sync-user dimaseuro0706@gmail.com
 */
class SyncSupabaseUser extends Command
{
    protected $signature = 'supabase:sync-user {email : Email akun Supabase Auth (boleh beda dari email user lokal)} {--password= : Password mobile (tanpa flag ini akan diminta interaktif)}';

    protected $description = 'Sinkronkan user lokal ke Supabase Auth (buat/reset password) dan pastikan supabase_uid ter-link';

    public function handle(SupabaseSyncService $supabase): int
    {
        if (! $supabase->isConfigured()) {
            $this->error('Supabase belum dikonfigurasi (SUPABASE_URL / SUPABASE_SERVICE_ROLE_KEY).');

            return self::FAILURE;
        }

        $email = strtolower($this->argument('email'));
        $password = $this->option('password');

        if (! $password) {
            $password = $this->secret('Password untuk login mobile (sama dengan web)');
        }

        if (! $password || strlen($password) < 6) {
            $this->error('Password minimal 6 karakter.');

            return self::FAILURE;
        }

        $auth = $supabase->findAuthUser($email);
        $uid = data_get($auth, 'id');

        // Match the local user either by (a) same email, or (b) the linked
        // supabase_uid (the mobile account may use a different email address).
        $user = User::where('email', $email)->first()
            ?? ($uid ? User::where('supabase_uid', $uid)->first() : null);

        if (! $user) {
            $this->error("Tidak ada user lokal dengan email '{$email}' atau supabase_uid ter-link.");

            return self::FAILURE;
        }

        if ($auth && $uid) {
            if (! $supabase->updateAuthUserPassword($uid, $password)) {
                $this->error('Gagal reset password di Supabase Auth (Admin API).');

                return self::FAILURE;
            }
            $this->info("Akun Supabase Auth ditemukan ({$email}) — password berhasil di-reset.");
        } else {
            $uid = $supabase->createAuthUser($email, $password, $user->name);
            if (! $uid) {
                $this->error('Gagal membuat akun Supabase Auth (Admin API).');

                return self::FAILURE;
            }
            $this->info("Akun Supabase Auth baru dibuat ({$email}).");
        }

        // Mirror users row (bigint id = MySQL id) so RLS / current_user_id() works.
        $supabase->upsert('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'supabase_uid' => $uid,
            'password' => null,
            'email_verified_at' => now()->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String() ?? now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ], 'supabase_uid');

        $user->forceFill(['supabase_uid' => $uid])->save();
        $this->info("users.supabase_uid = {$uid} tersimpan di kedua sisi.");

        if ($supabase->verifyLogin($email, $password)) {
            $this->info('Verifikasi login ke Supabase: BERHASIL ✔');
        } else {
            $this->warn('Verifikasi login ke Supabase gagal — cek kembali password / status project.');
        }

        $this->newLine();
        $this->line('Catatan: pastikan queue worker aktif (composer dev) agar sinkronisasi data otomatis berjalan.');

        return self::SUCCESS;
    }
}
