<?php

namespace Modules\QualityControl\Livewire;

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

    public function render()
    {
        return view('livewire.checklist-progress', [
            'progress' => $this->service->getProgress($this->checklist->id),
            'readiness' => $this->service->getDeployReadiness($this->checklist->id),
        ]);
    }
}