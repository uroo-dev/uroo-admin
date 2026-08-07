<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_stats_and_quick_actions(): void
    {
        $user = $this->makeUser();
        $client = Client::create(['user_id' => $user->id, 'name' => 'PT Contoh', 'status' => 'deal']);

        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'Website A', 'status' => 'development']);
        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'Website Selesai', 'status' => 'completed']);

        Invoice::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'invoice_number' => 'INV-TEST-0001',
            'items' => [],
            'total' => 1000000,
            'paid_amount' => 0,
            'status' => 'hutang',
            'due_date' => now()->addDays(7),
        ]);

        $user->savingsGoals()->create(['name' => 'Tabungan', 'target_amount' => 1000000, 'current_amount' => 250000]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Active Projects')
            ->assertSee('Pending Invoices')
            ->assertSee('Manage Projects')
            ->assertSee('INV-TEST-0001')
            ->assertSee('Rp 250.000');
    }

    public function test_active_projects_excludes_completed_and_archived(): void
    {
        $user = $this->makeUser();
        $client = Client::create(['user_id' => $user->id, 'name' => 'Client', 'status' => 'deal']);

        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'A', 'status' => 'development']);
        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'B', 'status' => 'testing']);
        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'C', 'status' => 'completed']);
        Project::create(['user_id' => $user->id, 'client_id' => $client->id, 'name' => 'D', 'status' => 'archived']);

        $stats = app(DashboardService::class)->getStats($user->id);
        $this->assertSame(2, $stats['active_projects']);

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
    }
}
