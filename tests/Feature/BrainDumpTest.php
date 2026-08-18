<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrainDumpTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('brain-dumps.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_brain_dumps(): void
    {
        $user = $this->makeUser();
        $user->brainDumps()->create(['content' => 'Remember to deploy tonight']);

        $this->actingAs($user)
            ->get(route('brain-dumps.index'))
            ->assertOk()
            ->assertSee('Remember to deploy tonight');
    }

    public function test_user_can_store_brain_dump(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('brain-dumps.store'), ['content' => 'New thought'])
            ->assertRedirect(route('brain-dumps.index'));

        $this->assertDatabaseHas('brain_dumps', [
            'user_id' => $user->id,
            'content' => 'New thought',
            'is_pinned' => false,
        ]);
    }

    public function test_user_can_update_brain_dump(): void
    {
        $user = $this->makeUser();
        $dump = $user->brainDumps()->create(['content' => 'Old thought']);

        $this->actingAs($user)
            ->put(route('brain-dumps.update', $dump), ['content' => 'Updated thought']);

        $dump->refresh();
        $this->assertSame('Updated thought', $dump->content);
    }

    public function test_user_can_toggle_pin(): void
    {
        $user = $this->makeUser();
        $dump = $user->brainDumps()->create(['content' => 'Pin me']);

        $this->actingAs($user)->patch(route('brain-dumps.toggle-pin', $dump));
        $dump->refresh();
        $this->assertTrue($dump->is_pinned);
    }

    public function test_user_can_toggle_archive(): void
    {
        $user = $this->makeUser();
        $dump = $user->brainDumps()->create(['content' => 'Archive me']);

        $this->actingAs($user)->patch(route('brain-dumps.toggle-archive', $dump));
        $dump->refresh();
        $this->assertTrue($dump->is_archived);
    }

    public function test_archived_brain_dump_is_hidden_from_index(): void
    {
        $user = $this->makeUser();
        $user->brainDumps()->create(['content' => 'Hidden dump', 'is_archived' => true]);

        $this->actingAs($user)
            ->get(route('brain-dumps.index'))
            ->assertOk()
            ->assertDontSee('Hidden dump');
    }

    public function test_user_can_delete_brain_dump(): void
    {
        $user = $this->makeUser();
        $dump = $user->brainDumps()->create(['content' => 'Delete me']);

        $this->actingAs($user)
            ->delete(route('brain-dumps.destroy', $dump))
            ->assertRedirect(route('brain-dumps.index'));

        $this->assertSoftDeleted('brain_dumps', ['id' => $dump->id]);
    }
}
