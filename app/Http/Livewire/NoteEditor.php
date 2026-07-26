<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Note;

class NoteEditor extends Component
{
    public ?int $noteId = null;
    public string $title = '';
    public string $content = '';
    public string $category = '';
    public array $tags = [];
    public string $tagInput = '';
    public bool $isPinned = false;
    public bool $isFavorite = false;

    public bool $isEdit = false;
    public bool $showPreview = false;

    protected $listeners = ['editNote' => 'loadNote'];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->loadNote($id);
        }
    }

    public function loadNote(int $id): void
    {
        $note = Note::findOrFail($id);
        $this->noteId = $note->id;
        $this->title = $note->title;
        $this->content = $note->content;
        $this->category = $note->category ?? '';
        $this->tags = $note->tags ?? [];
        $this->isPinned = $note->is_pinned;
        $this->isFavorite = $note->is_favorite;
        $this->isEdit = true;
    }

    public function addTag(): void
    {
        $tag = trim($this->tagInput);
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        $this->tagInput = '';
    }

    public function removeTag(int $index): void
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function autoSave(): void
    {
        if (!$this->title && !$this->content) {
            return;
        }

        if ($this->isEdit) {
            Note::findOrFail($this->noteId)->update([
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category ?: null,
                'tags' => $this->tags,
            ]);
            $this->dispatch('swal:success', title: 'Auto-saved');
        }
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:255',
            'isPinned' => 'boolean',
            'isFavorite' => 'boolean',
        ]);

        $data = [
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'category' => $this->category ?: null,
            'tags' => $this->tags,
            'is_pinned' => $this->isPinned,
            'is_favorite' => $this->isFavorite,
        ];

        if ($this->isEdit) {
            Note::findOrFail($this->noteId)->update($data);
            $this->dispatch('swal:success', title: 'Note berhasil diperbarui');
        } else {
            Note::create($data);
            $this->dispatch('swal:success', title: 'Note berhasil dibuat');
        }

        $this->dispatch('note-saved');
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'noteId', 'title', 'content', 'category', 'tags',
            'tagInput', 'isPinned', 'isFavorite', 'isEdit', 'showPreview',
        ]);
    }

    public function render()
    {
        return view('livewire.note-editor');
    }
}
