<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Thin client for Supabase REST (PostgREST) used by the Laravel web app to
 * keep the Flutter mobile app's Supabase data in sync (2-way).
 *
 * Uses the service role key (bypasses RLS) for server-side writes.
 */
class SupabaseSyncService
{
    public function isConfigured(): bool
    {
        return filled(config('supabase.url')) && filled(config('supabase.service_role_key'));
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl(rtrim(config('supabase.url'), '/'))
            ->withHeaders([
                'apikey' => config('supabase.service_role_key'),
                'Authorization' => 'Bearer '.config('supabase.service_role_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates,return=minimal',
            ]);
    }

    /**
     * Upsert a single row keyed by the given unique column (default "id").
     */
    public function upsert(string $table, array $payload, string $onConflict = 'id'): void
    {
        if (! $this->isConfigured()) {
            return;
        }
        $this->client()
            ->withQueryParameters(['on_conflict' => $onConflict])
            ->post('/rest/v1/'.$table, $payload)
            ->throw();
    }

    /**
     * Hard-delete a row by id.
     */
    public function delete(string $table, int $id): void
    {
        if (! $this->isConfigured()) {
            return;
        }
        $this->client()
            ->delete('/rest/v1/'.$table.'?id=eq.'.$id)
            ->throw();
    }

    /**
     * Fetch rows updated after the watermark, oldest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pull(string $table, ?string $watermark, int $limit = 500): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        $query = [
            'select' => '*',
            'order' => 'updated_at.asc',
            'limit' => (string) $limit,
        ];

        if ($watermark) {
            $query['updated_at'] = 'gt.'.Str::before($watermark, '+');
        }

        return $this->client()
            ->get('/rest/v1/'.$table.'?'.http_build_query($query))
            ->throw()
            ->json();
    }

    /**
     * Return the exact number of rows in a Supabase table (via Content-Range).
     */
    public function count(string $table): ?int
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $resp = $this->client()
            ->withHeaders(['Prefer' => 'count=exact', 'Range' => '0-0'])
            ->get('/rest/v1/'.$table.'?select=id&limit=0');

        if (! $resp->successful()) {
            return null;
        }

        $range = $resp->header('Content-Range');

        return Str::contains($range, '/') ? (int) Str::afterLast($range, '/') : null;
    }

    /**
     * Fetch a single sample row (used by the audit to compare columns).
     *
     * @return array<string, mixed>|null
     */
    public function firstRow(string $table): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $resp = $this->client()->get('/rest/v1/'.$table.'?select=*&limit=1');

        if (! $resp->successful()) {
            return null;
        }

        $body = $resp->json();

        return is_array($body) ? ($body[0] ?? null) : null;
    }

    /**
     * Admin API: create a Supabase Auth user and return its UUID.
     */
    public function createAuthUser(string $email, string $password, string $name): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $resp = $this->authClient()->post('/auth/v1/admin/users', [
            'email' => $email,
            'password' => $password,
            'email_confirm' => true,
            'user_metadata' => ['name' => $name],
            'data' => ['name' => $name],
        ]);

        if ($resp->successful()) {
            return data_get($resp->json(), 'id');
        }

        // The user may already exist by that email — attempt the banned trick is
        // not needed; just return null and continue (best effort).
        return null;
    }

    /**
     * Admin API: find a Supabase Auth user by email.
     *
     * @return array<string, mixed>|null
     */
    public function findAuthUser(string $email): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $page = 1;
        $perPage = 200;

        do {
            $resp = $this->authClient()->get('/auth/v1/admin/users', [
                'page' => $page,
                'per_page' => $perPage,
            ]);

            if (! $resp->successful()) {
                return null;
            }

            $payload = $resp->json();
            $users = $payload['users'] ?? [];

            foreach ($users as $user) {
                if (strcasecmp((string) data_get($user, 'email'), $email) === 0) {
                    return $user;
                }
            }

            $total = (int) ($payload['total'] ?? 0);
            $page++;
        } while (($page - 1) * $perPage < $total && $users !== []);

        return null;
    }

    /**
     * Admin API: set a new password for an existing Supabase Auth user.
     */
    public function updateAuthUserPassword(string $uid, string $password): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        return $this->authClient()
            ->put('/auth/v1/admin/users/'.$uid, ['password' => $password])
            ->successful();
    }

    /**
     * Verify credentials against GoTrue and return the access token on success.
     */
    public function verifyLogin(string $email, string $password): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        $resp = $this->authClient()->post('/auth/v1/token?grant_type=password', [
            'email' => $email,
            'password' => $password,
        ]);

        return $resp->successful() && filled(data_get($resp->json(), 'access_token'));
    }

    /**
     * Client for the GoTrue Admin API (same headers as the REST client).
     */
    protected function authClient(): PendingRequest
    {
        return Http::baseUrl(config('supabase.url'))
            ->withHeaders([
                'apikey' => config('supabase.service_role_key'),
                'Authorization' => 'Bearer '.config('supabase.service_role_key'),
                'Content-Type' => 'application/json',
            ]);
    }
}
