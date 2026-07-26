<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\QualityChecklist;
use App\Services\QualityControlService;

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
