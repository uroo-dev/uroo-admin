<?php

namespace Tests\Feature;

use App\Models\AppIdea;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdeaTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function storePayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'My App Idea',
            'tagline' => 'A great idea',
            'description' => 'Doing something new',
            'features' => "Login\nDashboard",
            'tech_stack' => "Laravel\nVue",
            'platform' => 'web',
            'status' => 'draft',
            'priority' => 'medium',
            'tags' => 'react, vue',
            'notes' => 'Some notes',
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('ideas.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_ideas(): void
    {
        $user = $this->makeUser();
        $user->ideas()->create([
            'name' => 'Task Tracker',
            'tagline' => 'Track tasks',
            'status' => 'development',
            'priority' => 'high',
            'features' => ['a'],
            'tech_stack' => ['php'],
        ]);

        $this->actingAs($user)
            ->get(route('ideas.index'))
            ->assertOk()
            ->assertSee('Task Tracker');
    }

    public function test_user_can_create_idea_with_string_arrays(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('ideas.store'), $this->storePayload())
            ->assertRedirect(route('ideas.index'));

        $idea = AppIdea::first();
        $this->assertNotNull($idea);
        $this->assertSame(['Login', 'Dashboard'], $idea->features);
        $this->assertSame(['Laravel', 'Vue'], $idea->tech_stack);
        $this->assertSame(['react', 'vue'], $idea->tags);
        $this->assertSame($user->id, $idea->user_id);
    }

    public function test_user_can_create_idea_without_optional_fields(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('ideas.store'), $this->storePayload([
                'features' => '',
                'tech_stack' => '',
                'tags' => '',
            ]));

        $idea = AppIdea::first();
        $this->assertNotNull($idea);
        $this->assertSame([], $idea->features);
        $this->assertSame([], $idea->tech_stack);
        $this->assertSame([], $idea->tags);
    }

    public function test_user_can_update_idea(): void
    {
        $user = $this->makeUser();
        $idea = $user->ideas()->create([
            'name' => 'Old',
            'status' => 'draft',
            'priority' => 'low',
            'features' => ['a'],
        ]);

        $this->actingAs($user)
            ->put(route('ideas.update', $idea), $this->storePayload([
                'name' => 'Updated',
                'tags' => 'x, y',
            ]));

        $idea->refresh();
        $this->assertSame('Updated', $idea->name);
        $this->assertSame(['x', 'y'], $idea->tags);
    }

    public function test_user_can_delete_idea(): void
    {
        $user = $this->makeUser();
        $idea = $user->ideas()->create([
            'name' => 'Delete Me',
            'status' => 'draft',
            'priority' => 'low',
        ]);

        $this->actingAs($user)
            ->delete(route('ideas.destroy', $idea))
            ->assertRedirect(route('ideas.index'));

        $this->assertDatabaseMissing('app_ideas', ['id' => $idea->id]);
    }
}
