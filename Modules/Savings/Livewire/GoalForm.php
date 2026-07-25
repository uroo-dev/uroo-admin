<?php

declare(strict_types=1);

namespace Modules\Savings\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Savings\Models\SavingsGoal;

class GoalForm extends Component
{
    public ?SavingsGoal $goal = null;

    public string $name = '';

    public float $targetAmount = 0;

    public ?string $icon = null;

    public ?string $color = null;

    public ?string $deadline = null;

    public ?string $notes = null;

    public bool $isEditing = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'targetAmount' => 'required|numeric|min:1',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(?SavingsGoal $goal = null): void
    {
        $this->goal = $goal;

        if ($goal && $goal->exists) {
            $this->isEditing = true;
            $this->name = $goal->name;
            $this->targetAmount = (float) $goal->target_amount;
            $this->icon = $goal->icon;
            $this->color = $goal->color;
            $this->deadline = $goal->deadline?->format('Y-m-d');
            $this->notes = $goal->notes;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'target_amount' => $this->targetAmount,
            'icon' => $this->icon,
            'color' => $this->color,
            'deadline' => $this->deadline,
            'notes' => $this->notes,
        ];

        if ($this->isEditing && $this->goal) {
            $this->goal->update($data);
        } else {
            SavingsGoal::create($data);
        }

        $this->dispatch('goal-saved');
        $this->redirect(route('savings.index'));
    }

    public function render(): View
    {
        return view('savings::livewire.goal-form');
    }
}
