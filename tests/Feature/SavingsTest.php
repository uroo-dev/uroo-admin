<?php

namespace Tests\Feature;

use App\Models\SavingsTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function goalPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'New Laptop',
            'target_amount' => 10000000,
            'icon' => 'bx bx-laptop',
            'color' => '#4F46E5',
            'deadline' => null,
            'notes' => 'For work',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('savings.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_goals_and_stats(): void
    {
        $user = $this->makeUser();
        $user->savingsGoals()->create([
            'name' => 'Vacation',
            'target_amount' => 5000000,
            'current_amount' => 1000000,
        ]);

        $this->actingAs($user)
            ->get(route('savings.index'))
            ->assertOk()
            ->assertSee('Vacation')
            ->assertSee('Total Goals')
            ->assertSee('Rp 1.000.000');
    }

    public function test_user_can_create_goal(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('savings.store'), $this->goalPayload())
            ->assertRedirect(route('savings.index'));

        $this->assertDatabaseHas('savings_goals', [
            'user_id' => $user->id,
            'name' => 'New Laptop',
            'target_amount' => 10000000,
            'current_amount' => 0,
        ]);
    }

    public function test_user_can_update_goal(): void
    {
        $user = $this->makeUser();
        $goal = $user->savingsGoals()->create([
            'name' => 'Old Goal',
            'target_amount' => 1000000,
        ]);

        $this->actingAs($user)
            ->put(route('savings.update', $goal), $this->goalPayload(['target_amount' => 2500000]));

        $goal->refresh();
        $this->assertSame('New Laptop', $goal->name);
        $this->assertEquals(2500000, (float) $goal->target_amount);
    }

    public function test_deposit_increments_amount_and_completes_when_target_reached(): void
    {
        $user = $this->makeUser();
        $goal = $user->savingsGoals()->create([
            'name' => 'Emergency Fund',
            'target_amount' => 1000000,
            'current_amount' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('savings.deposit', $goal), ['amount' => 1000000, 'description' => 'Bonus']);

        $goal->refresh();
        $this->assertEquals(1000000, (float) $goal->current_amount);
        $this->assertTrue($goal->is_completed);
        $this->assertEquals(1, SavingsTransaction::count());
    }

    public function test_withdraw_decreases_and_reopens_goal(): void
    {
        $user = $this->makeUser();
        $goal = $user->savingsGoals()->create([
            'name' => 'Goal Fund',
            'target_amount' => 1000000,
            'current_amount' => 1000000,
            'is_completed' => true,
        ]);

        $this->actingAs($user)
            ->post(route('savings.withdraw', $goal), ['amount' => 300000]);

        $goal->refresh();
        $this->assertEquals(700000, (float) $goal->current_amount);
        $this->assertFalse($goal->is_completed);
        $this->assertEquals(1, SavingsTransaction::count());
    }

    public function test_withdraw_allows_amount_beyond_current_balance(): void
    {
        $user = $this->makeUser();
        $goal = $user->savingsGoals()->create([
            'name' => 'Goal',
            'target_amount' => 1000000,
            'current_amount' => 100000,
        ]);

        $this->actingAs($user)
            ->post(route('savings.withdraw', $goal), ['amount' => 500000]);

        $goal->refresh();
        $this->assertEquals(-400000, (float) $goal->current_amount);
        $this->assertFalse($goal->is_completed);
    }

    public function test_user_can_delete_goal_with_transactions(): void
    {
        $user = $this->makeUser();
        $goal = $user->savingsGoals()->create([
            'name' => 'Delete Me',
            'target_amount' => 1000000,
        ]);
        SavingsTransaction::create(['goal_id' => $goal->id, 'type' => 'deposit', 'amount' => 1000]);

        $this->actingAs($user)
            ->delete(route('savings.destroy', $goal))
            ->assertRedirect(route('savings.index'));

        $this->assertSoftDeleted('savings_goals', ['id' => $goal->id]);
        $this->assertEquals(0, SavingsTransaction::count());
    }
}
