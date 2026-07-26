<?php

namespace App\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BrainDump;

class DumpList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $newContent = '';
    public ?int $deleteId = null;

    public function mount(): void
    {
        //
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
        $this->resetPage();
        $this->dispatch('swal:success', title: 'Brain dump berhasil disimpan');
    }

    public function autoSave(string $content, int $dumpId): void
    {
        $dump = BrainDump::where('user_id', auth()->id())->findOrFail($dumpId);
        $dump->update(['content' => $content]);
    }

    public function togglePin(int $id): void
    {
        $dump = BrainDump::where('user_id', auth()->id())->findOrFail($id);
        $dump->update(['is_pinned' => !$dump->is_pinned]);
    }

    public function archive(int $id): void
    {
        BrainDump::where('user_id', auth()->id())->findOrFail($id)->update(['is_archived' => true]);
        $this->dispatch('swal:success', title: 'Brain dump diarsipkan');
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->dispatch('swal:confirm', ['event' => 'delete-dump', 'title' => 'Hapus catatan?', 'confirmText' => 'Ya, hapus!']);
    }

    #[On('delete-dump')]
    public function delete(): void
    {
        if ($this->deleteId) {
            BrainDump::where('user_id', auth()->id())->findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('swal:success', title: 'Brain dump dihapus');
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = BrainDump::where('user_id', auth()->id())
            ->where('is_archived', false);

        if ($this->search) {
            $query->where('content', 'like', "%{$this->search}%");
        }

        $pinnedDumps = (clone $query)->where('is_pinned', true)
            ->orderBy('updated_at', 'desc')
            ->get();

        $dumps = $query->where('is_pinned', false)
            ->orderBy('updated_at', 'desc')
            ->paginate(30);

        return view('livewire.dump-list', compact('dumps', 'pinnedDumps'));
    }
}
