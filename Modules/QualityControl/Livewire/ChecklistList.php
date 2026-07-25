<?php

declare(strict_types=1);

namespace Modules\QualityControl\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\QualityControl\Models\ChecklistItem;
use Modules\QualityControl\Models\QualityChecklist;
use Modules\QualityControl\Services\QualityControlService;

class ChecklistList extends Component
{
    public QualityChecklist $checklist;

    public string $newItemLabel = '';

    public Collection $items;

    public QualityControlService $service;

    public function boot(QualityControlService $service): void
    {
        $this->service = $service;
    }

    public function mount(QualityChecklist $checklist): void
    {
        $this->checklist = $checklist;
        $this->items = $checklist->items()->orderBy('sort_order')->get();
    }

    public function addItem(): void
    {
        $this->validate([
            'newItemLabel' => 'required|string|max:255',
        ]);

        $maxSort = $this->checklist->items()->max('sort_order') ?? 0;

        ChecklistItem::create([
            'checklist_id' => $this->checklist->id,
            'label' => $this->newItemLabel,
            'is_checked' => false,
            'sort_order' => $maxSort + 1,
        ]);

        $this->newItemLabel = '';
        $this->refreshItems();
    }

    public function toggleItem(int $itemId): void
    {
        $item = ChecklistItem::findOrFail($itemId);
        $item->update(['is_checked' => ! $item->is_checked]);
        $this->refreshItems();
    }

    public function removeItem(int $itemId): void
    {
        ChecklistItem::findOrFail($itemId)->delete();
        $this->refreshItems();
    }

    public function updateSortOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            ChecklistItem::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        $this->refreshItems();
    }

    #[On('refresh-checklist')]
    public function refreshItems(): void
    {
        $this->items = $this->checklist->items()->orderBy('sort_order')->get();
    }

    public function getProgressProperty(): int
    {
        return $this->service->getProgress($this->checklist->id);
    }

    public function getDeployReadinessProperty(): string
    {
        return $this->service->getDeployReadiness($this->checklist->id);
    }

    public function render(): View
    {
        return view('quality-control::livewire.checklist-list');
    }
}
