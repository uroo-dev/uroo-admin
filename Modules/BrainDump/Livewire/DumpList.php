<?php

declare(strict_types=1);

namespace Modules\BrainDump\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\BrainDump\Models\BrainDump;

class DumpList extends Component
{
    public string $search = '';

    public string $newContent = '';

    public Collection $dumps;

    public function mount(): void
    {
        $this->loadDumps();
    }

    public function loadDumps(): void
    {
        $query = BrainDump::where('user_id', auth()->id())
            ->active()
            ->when($this->search, function ($q) {
                $q->where('content', 'like', "%{$this->search}%");
            });

        $pinned = (clone $query)->pinned()->orderBy('updated_at', 'desc')->get();
        $unpinned = (clone $query)->where('is_pinned', false)->orderBy('created_at', 'desc')->get();

        $this->dumps = $pinned->merge($unpinned);
    }

    public function quickCreate(): void
    {
        $this->validate([
            'newContent' => 'required|string|max:5000',
        ]);

        BrainDump::create([
            'user_id' => auth()->id(),
            'content' => $this->newContent,
            'is_pinned' => false,
            'is_archived' => false,
        ]);

        $this->newContent = '';
        $this->loadDumps();
    }

    public function autoSave(string $content, int $dumpId): void
    {
        $dump = BrainDump::where('user_id', auth()->id())->findOrFail($dumpId);
        $dump->update(['content' => $content]);
        $this->loadDumps();
    }

    public function togglePin(int $id): void
    {
        $dump = BrainDump::where('user_id', auth()->id())->findOrFail($id);
        $dump->update(['is_pinned' => ! $dump->is_pinned]);
        $this->loadDumps();
    }

    public function archive(int $id): void
    {
        $dump = BrainDump::where('user_id', auth()->id())->findOrFail($id);
        $dump->update(['is_archived' => true]);
        $this->loadDumps();
    }

    public function updatedSearch(): void
    {
        $this->loadDumps();
    }

    public function render(): View
    {
        return view('braindump::livewire.dump-list', [
            'dumps' => $this->dumps,
        ]);
    }
}
