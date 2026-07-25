<?php

declare(strict_types=1);

namespace Modules\QualityControl\Services;

use Modules\QualityControl\Models\ChecklistItem;
use Modules\QualityControl\Models\QualityChecklist;

class QualityControlService
{
    public function getProgress(int $checklistId): int
    {
        $total = ChecklistItem::where('checklist_id', $checklistId)->count();

        if ($total === 0) {
            return 0;
        }

        $checked = ChecklistItem::where('checklist_id', $checklistId)
            ->where('is_checked', true)
            ->count();

        return (int) round(($checked / $total) * 100);
    }

    public function getDeployReadiness(int $checklistId): string
    {
        $progress = $this->getProgress($checklistId);

        return match (true) {
            $progress === 100 => 'ready',
            $progress >= 75   => 'almost_ready',
            $progress >= 50   => 'in_progress',
            $progress >= 25   => 'started',
            default           => 'not_ready',
        };
    }

    public function getAllProgress(int $userId): array
    {
        $checklists = QualityChecklist::where('user_id', $userId)
            ->withCount(['items', 'items as checked_items_count' => function ($query) {
                $query->where('is_checked', true);
            }])
            ->get();

        return $checklists->map(function ($checklist) {
            $progress = $checklist->items_count > 0
                ? (int) round(($checklist->checked_items_count / $checklist->items_count) * 100)
                : 0;

            return [
                'id' => $checklist->id,
                'title' => $checklist->title,
                'progress' => $progress,
                'readiness' => $this->getDeployReadiness($checklist->id),
            ];
        })->toArray();
    }
}
