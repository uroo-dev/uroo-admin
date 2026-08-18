<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['password' => bcrypt('rahasia123')]);

        $this->token = $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => 'rahasia123',
        ])->json('token');

        $this->assertIsString($this->token);
    }

    public function test_health_is_open(): void
    {
        $this->getJson('/api/v1/health')->assertOk()->assertJson(['ok' => true]);
    }

    public function test_login_rejects_wrong_password(): void
    {
        $this->postJson('/api/v1/login', [
            'email' => $this->user->email,
            'password' => 'salah',
        ])->assertUnauthorized();
    }

    public function test_pull_requires_token(): void
    {
        $this->getJson('/api/v1/sync/pull')->assertUnauthorized();
    }

    public function test_push_creates_rows_and_remaps_temp_ids(): void
    {
        $response = $this->withToken($this->token)->postJson('/api/v1/sync/push', [
            'tables' => [
                'clients' => [[
                    'temp_id' => -1,
                    'name' => 'PT Sync Test',
                    'status' => 'deal',
                    'user_id' => 999, // must be forced to the token owner
                    'updated_at' => now()->toIso8601String(),
                ]],
                'projects' => [[
                    'temp_id' => -2,
                    'client_id' => -1,
                    'name' => 'Proyek Sync',
                    'status' => 'development',
                    'user_id' => 999,
                    'updated_at' => now()->toIso8601String(),
                ]],
            ],
        ])->assertOk();

        $created = $response->json('created');
        $clientId = $created['clients'][0]['id'];
        $projectId = $created['projects'][0]['id'];

        $this->assertDatabaseHas('clients', ['id' => $clientId, 'name' => 'PT Sync Test', 'user_id' => $this->user->id]);
        $this->assertDatabaseHas('projects', ['id' => $projectId, 'client_id' => $clientId, 'user_id' => $this->user->id]);
    }

    public function test_push_updates_existing_row_and_ignores_stale_ones(): void
    {
        $client = Client::create([
            'user_id' => $this->user->id,
            'name' => 'Nama Lama',
            'status' => 'deal',
        ]);

        // Stale: older updated_at than the server row -> must be skipped.
        $this->withToken($this->token)->postJson('/api/v1/sync/push', [
            'tables' => [
                'clients' => [[
                    'id' => $client->id,
                    'name' => 'Nama Basi',
                    'user_id' => $this->user->id,
                    'updated_at' => $client->updated_at->subMinutes(5)->toIso8601String(),
                ]],
            ],
        ]);
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Nama Lama']);

        // Fresh: newer updated_at -> applied.
        $this->withToken($this->token)->postJson('/api/v1/sync/push', [
            'tables' => [
                'clients' => [[
                    'id' => $client->id,
                    'name' => 'Nama Baru',
                    'user_id' => $this->user->id,
                    'updated_at' => $client->updated_at->addMinutes(5)->toIso8601String(),
                ]],
            ],
        ]);
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Nama Baru']);
    }

    public function test_push_tombstone_soft_deletes_server_row(): void
    {
        $note = Note::create([
            'user_id' => $this->user->id,
            'title' => 'Catatan',
            'content' => 'isi',
        ]);

        $this->withToken($this->token)->postJson('/api/v1/sync/push', [
            'tables' => [
                'notes' => [[
                    'id' => $note->id,
                    'title' => 'Catatan',
                    'content' => 'isi',
                    'user_id' => $this->user->id,
                    'updated_at' => $note->updated_at->addMinute()->toIso8601String(),
                    'deleted_at' => now()->toIso8601String(),
                ]],
            ],
        ]);

        $this->assertSoftDeleted('notes', ['id' => $note->id]);
    }

    public function test_push_rejects_rows_from_other_users(): void
    {
        $other = User::factory()->create();
        $client = Client::create([
            'user_id' => $other->id,
            'name' => 'Punya orang lain',
            'status' => 'deal',
        ]);

        $this->withToken($this->token)->postJson('/api/v1/sync/push', [
            'tables' => [
                'clients' => [[
                    'id' => $client->id,
                    'name' => 'Hack Attempt',
                    'user_id' => $other->id,
                    'updated_at' => $client->updated_at->addMinute()->toIso8601String(),
                ]],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Punya orang lain']);
    }

    public function test_pull_returns_only_rows_changed_since_watermark(): void
    {
        Client::create(['user_id' => $this->user->id, 'name' => 'Lama', 'status' => 'deal']);

        $older = Client::create(['user_id' => $this->user->id, 'name' => 'Baru', 'status' => 'deal']);
        $older->updated_at = now()->addMinutes(10);
        $older->save();

        $since = $older->updated_at->subMinutes(3)->toIso8601String();

        $response = $this->withToken($this->token)
            ->getJson('/api/v1/sync/pull?since='.urlencode($since))
            ->assertOk();

        $names = collect($response->json('tables.clients'))->pluck('name');

        $this->assertFalse($names->contains('Lama'));
        $this->assertTrue($names->contains('Baru'));
    }

    public function test_pull_includes_soft_deleted_rows(): void
    {
        $note = Note::create(['user_id' => $this->user->id, 'title' => 'Hapus', 'content' => 'x']);
        $note->delete();

        $response = $this->withToken($this->token)
            ->getJson('/api/v1/sync/pull')
            ->assertOk();

        $rows = collect($response->json('tables.notes'));

        $this->assertTrue($rows->contains(fn ($r) => $r['id'] === $note->id && $r['deleted_at'] !== null));
    }

    public function test_pull_scopes_children_via_parent_owner(): void
    {
        $other = User::factory()->create();
        Client::create(['user_id' => $other->id, 'name' => 'Orang lain', 'status' => 'deal']);
        $invoice = Invoice::create([
            'user_id' => $this->user->id,
            'client_id' => Client::create(['user_id' => $this->user->id, 'name' => 'Saya', 'status' => 'deal'])->id,
            'invoice_number' => 'INV-1',
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'items' => [],
            'subtotal' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'status' => 'hutang',
        ]);

        $response = $this->withToken($this->token)->getJson('/api/v1/sync/pull')->assertOk();

        $this->assertCount(1, $response->json('tables.clients'));
        $this->assertCount(1, $response->json('tables.invoices'));
        $this->assertSame($invoice->id, $response->json('tables.invoices.0.id'));
    }
}
