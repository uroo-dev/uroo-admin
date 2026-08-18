<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function storePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'My Test Note',
            'content' => 'Some content',
            'category' => 'Work',
            'tags' => 'php, laravel',
            'is_pinned' => false,
            'is_favorite' => false,
        ], $overrides);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('notes.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_notes(): void
    {
        $user = $this->makeUser();
        $user->notes()->create([
            'title' => 'Design System',
            'content' => 'Neo brutalism',
            'category' => 'Design',
            'tags' => ['ui', 'css'],
            'is_pinned' => true,
        ]);

        $this->actingAs($user)
            ->get(route('notes.index'))
            ->assertOk()
            ->assertSee('Design System')
            ->assertSee('Design');
    }

    public function test_user_can_create_note_with_comma_separated_tags(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('notes.store'), $this->storePayload())
            ->assertRedirect(route('notes.index'));

        $note = Note::first();
        $this->assertNotNull($note);
        $this->assertSame(['php', 'laravel'], $note->tags);
        $this->assertSame($user->id, $note->user_id);
    }

    public function test_user_can_create_note_without_tags(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('notes.store'), $this->storePayload(['tags' => '']));

        $note = Note::first();
        $this->assertNotNull($note);
        $this->assertSame([], $note->tags);
    }

    public function test_user_can_update_note_and_tags(): void
    {
        $user = $this->makeUser();
        $note = $user->notes()->create([
            'title' => 'Old Title',
            'content' => 'Old content',
            'category' => 'Work',
            'tags' => ['a'],
        ]);

        $this->actingAs($user)
            ->put(route('notes.update', $note), $this->storePayload([
                'title' => 'New Title',
                'tags' => 'x,  y , z',
            ]));

        $note->refresh();
        $this->assertSame('New Title', $note->title);
        $this->assertSame(['x', 'y', 'z'], $note->tags);
    }

    public function test_user_can_toggle_pin_and_favorite(): void
    {
        $user = $this->makeUser();
        $note = $user->notes()->create([
            'title' => 'Toggle',
            'content' => 'content',
        ]);

        $this->actingAs($user)->patch(route('notes.toggle-pin', $note));
        $note->refresh();
        $this->assertTrue($note->is_pinned);

        $this->actingAs($user)->patch(route('notes.toggle-favorite', $note));
        $note->refresh();
        $this->assertTrue($note->is_favorite);
    }

    public function test_user_can_delete_note(): void
    {
        $user = $this->makeUser();
        $note = $user->notes()->create([
            'title' => 'Delete Me',
            'content' => 'content',
        ]);

        $this->actingAs($user)
            ->delete(route('notes.destroy', $note))
            ->assertRedirect(route('notes.index'));

        $this->assertSoftDeleted('notes', ['id' => $note->id]);
    }
}
