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
     * Admin API: create a Supabase Auth user and return its UUID.
     */
    public function createAuthUser(string $email, string $password, string $name): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $resp = Http::baseUrl(config('supabase.url'))
            ->withHeaders([
                'apikey' => config('supabase.service_role_key'),
                'Authorization' => 'Bearer '.config('supabase.service_role_key'),
                'Content-Type' => 'application/json',
            ])
            ->post('/auth/v1/admin/users', [
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
}
