<?php

declare(strict_types=1);

namespace Modules\QualityControl\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\QualityControl\Models\QualityChecklist;
use Modules\QualityControl\Services\QualityControlService;

class ChecklistProgress extends Component
{
    public QualityChecklist $checklist;

    public QualityControlService $service;

    public function boot(QualityControlService $service): void
    {
        $this->service = $service;
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
        return view('quality-control::livewire.checklist-progress', [
            'progress' => $this->progress,
            'readiness' => $this->deployReadiness,
        ]);
    }
}
