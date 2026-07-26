<?php

namespace App\Http\Controllers;

use App\Models\ChecklistItem;
use App\Models\QualityChecklist;
use Illuminate\Http\Request;

class QualityControlController extends Controller
{
    public function index()
    {
        $checklists = QualityChecklist::where('user_id', auth()->id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('quality-control.index', compact('checklists'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'items'          => 'nullable|array',
            'items.*.label'  => 'required|string|max:255',
        ]);

        $checklist = QualityChecklist::create([
            'user_id' => auth()->id(),
            'title'   => $data['title'],
        ]);

        foreach ($data['items'] ?? [] as $index => $item) {
            if (!empty($item['label'])) {
                $checklist->items()->create([
                    'label'      => $item['label'],
                    'is_checked' => false,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('quality-control')->with('success', 'Checklist created successfully.');
    }

    public function update(Request $request, QualityChecklist $qualityChecklist)
    {
        $this->authorize('update', $qualityChecklist);

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'items'          => 'nullable|array',
            'items.*.label'  => 'required|string|max:255',
        ]);

        $qualityChecklist->update(['title' => $data['title']]);

        // Replace all items
        $qualityChecklist->items()->delete();
        foreach ($data['items'] ?? [] as $index => $item) {
            if (!empty($item['label'])) {
                $qualityChecklist->items()->create([
                    'label'      => $item['label'],
                    'is_checked' => false,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('quality-control')->with('success', 'Checklist updated successfully.');
    }

    public function destroy(QualityChecklist $qualityChecklist)
    {
        $this->authorize('delete', $qualityChecklist);
        $qualityChecklist->items()->delete();
        $qualityChecklist->delete();

        return redirect()->route('quality-control')->with('success', 'Checklist deleted successfully.');
    }

    public function toggleChecked(ChecklistItem $checklistItem)
    {
        $checklistItem->update(['is_checked' => !$checklistItem->is_checked]);
        return redirect()->back();
    }
}
