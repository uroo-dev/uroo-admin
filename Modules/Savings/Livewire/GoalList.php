<?php

namespace Modules\Savings\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Savings\Models\SavingsGoal;
use Modules\Savings\Models\SavingsTransaction;
use Modules\Savings\Services\SavingsService;

class GoalList extends Component
{
    use WithPagination;

    public ?int $selectedGoalId = null;
    public string $depositAmount = '';
    public string $withdrawAmount = '';
    public string $transactionDescription = '';

    public SavingsService $service;

    public function boot(SavingsService $service): void
    {
        $this->service = $service;
    }

    #[Computed]
    public function stats(): array
    {
        return $this->service->getStats(auth()->id());
    }

    public function selectGoal(int $goalId): void
    {
        $this->selectedGoalId = $goalId;
        $this->reset(['depositAmount', 'withdrawAmount', 'transactionDescription']);
    }

    public function deposit(): void
    {
        $this->validate(['depositAmount' => 'required|numeric|min:0.01']);

        $goal = SavingsGoal::where('user_id', auth()->id())->findOrFail($this->selectedGoalId);
        $amount = (float) $this->depositAmount;

        SavingsTransaction::create([
            'goal_id' => $goal->id,
            'type' => 'deposit',
            'amount' => $amount,
            'description' => $this->transactionDescription ?: null,
        ]);

        $goal->increment('current_amount', $amount);

        if ($goal->current_amount >= $goal->target_amount) {
            $goal->update(['is_completed' => true]);
        }

        $this->reset(['depositAmount', 'transactionDescription']);
        $this->dispatch('swal:success', title: 'Deposit berhasil');
    }

    public function withdraw(): void
    {
        $this->validate(['withdrawAmount' => 'required|numeric|min:0.01']);

        $goal = SavingsGoal::where('user_id', auth()->id())->findOrFail($this->selectedGoalId);
        $amount = (float) $this->withdrawAmount;

        if ($amount > $goal->current_amount) {
            $this->addError('withdrawAmount', 'Saldo tidak mencukupi.');
            return;
        }

        SavingsTransaction::create([
            'goal_id' => $goal->id,
            'type' => 'withdraw',
            'amount' => $amount,
            'description' => $this->transactionDescription ?: null,
        ]);

        $goal->decrement('current_amount', $amount);
        $goal->update(['is_completed' => false]);

        $this->reset(['withdrawAmount', 'transactionDescription']);
        $this->dispatch('swal:success', title: 'Withdraw berhasil');
    }

    public function render()
    {
        $goals = SavingsGoal::where('user_id', auth()->id())
            ->withCount('transactions')
            ->orderBy('is_completed', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('savings.index', [
            'goals' => $goals,
            'stats' => $this->stats,
        ]);
    }
}