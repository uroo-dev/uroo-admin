<?php

namespace Modules\Bookmark\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Bookmark\Models\Bookmark;

class BookmarkList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $category = '';
    public string $viewMode = 'grid';
    public bool $showFavorites = false;

    protected $queryString = ['search', 'category', 'viewMode', 'showFavorites'];

    public function render()
    {
        $query = Bookmark::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                  ->orWhere('url', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->showFavorites) {
            $query->where('is_favorite', true);
        }

        $bookmarks = $query->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(24);

        $categories = Bookmark::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('bookmarks.index', compact('bookmarks', 'categories'));
    }

    public function toggleFavorite(int $id): void
    {
        $bookmark = Bookmark::findOrFail($id);
        $bookmark->update(['is_favorite' => !$bookmark->is_favorite]);
    }

    public function deleteBookmark(int $id): void
    {
        Bookmark::findOrFail($id)->delete();
        $this->dispatch('swal:success', title: 'Bookmark berhasil dihapus');
    }

    public function copyLink(int $id): void
    {
        $bookmark = Bookmark::findOrFail($id);
        $this->dispatch('clipboard:copy', url: $bookmark->url);
        $this->dispatch('swal:success', title: 'Link copied to clipboard');
    }

    public function switchView(string $mode): void
    {
        $this->viewMode = $mode;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
}
