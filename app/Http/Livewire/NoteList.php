<?php

namespace App\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Note;

class NoteList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public bool $showFavorites = false;
    public ?int $deleteId = null;

    protected $queryString = ['search', 'category', 'showFavorites'];

    public function render()
    {
        $query = Note::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('content', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->showFavorites) {
            $query->where('is_favorite', true);
        }

        $notes = $query->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        $categories = Note::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('livewire.note-list', compact('notes', 'categories'));
    }

    public function togglePin(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['is_pinned' => !$note->is_pinned]);
    }

    public function toggleFavorite(int $id): void
    {
        $note = Note::findOrFail($id);
        $note->update(['is_favorite' => !$note->is_favorite]);
    }

    public function edit(int $id): void
    {
        $this->dispatch('editNote', id: $id);
        $this->dispatch('open-modal', id: 'note-form');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-note', 'title' => 'Hapus note?', 'confirmText' => 'Ya, hapus!']);
    }

    #[On('delete-note')]
    public function deleteNote(): void
    {
        if ($this->deleteId) {
            Note::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Note berhasil dihapus');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
