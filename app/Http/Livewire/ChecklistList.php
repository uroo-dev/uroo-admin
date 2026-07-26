<?php

namespace App\Http\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\ChecklistItem;
use App\Models\QualityChecklist;
use App\Services\QualityControlService;

class ChecklistList extends Component
{
    public QualityChecklist $checklist;
    public string $newItemLabel = '';
    public QualityControlService $service;

    public function boot(QualityControlService $service): void
    {
        $this->service = $service;
    }

    public function mount(QualityChecklist $checklist): void
    {
        $this->checklist = $checklist;
    }

    public function addItem(): void
    {
        $this->validate(['newItemLabel' => 'required|string|max:255']);

        $maxSort = $this->checklist->items()->max('sort_order') ?? 0;

        ChecklistItem::create([
            'checklist_id' => $this->checklist->id,
            'label' => $this->newItemLabel,
            'is_checked' => false,
            'sort_order' => $maxSort + 1,
        ]);

        $this->newItemLabel = '';
        $this->dispatch('refresh-checklist');
    }

    public function toggleItem(int $itemId): void
    {
        $item = ChecklistItem::findOrFail($itemId);
        $item->update(['is_checked' => !$item->is_checked]);
        $this->dispatch('refresh-checklist');
    }

    public function removeItem(int $itemId): void
    {
        ChecklistItem::findOrFail($itemId)->delete();
        $this->dispatch('swal:success', title: 'Item dihapus');
        $this->dispatch('refresh-checklist');
    }

    #[On('refresh-checklist')]
    public function refreshItems(): void
    {
        //
    }

    public function getProgressProperty(): int
    {
        return $this->service->getProgress($this->checklist->id);
    }

    public function getDeployReadinessProperty(): string
    {
        return $this->service->getDeployReadiness($this->checklist->id);
    }

    public function render()
    {
        return view('livewire.checklist-list', [
            'items' => $this->checklist->items()->orderBy('sort_order')->get(),
        ]);
    }
}
