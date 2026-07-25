<?php

declare(strict_types=1);

namespace Modules\Savings\Services;

use Modules\Savings\Models\SavingsGoal;
use Modules\Savings\Models\SavingsTransaction;

class SavingsService
{
    public function getStats(int $userId): array
    {
        $goals = SavingsGoal::where('user_id', $userId);

        $totalSaved = (clone $goals)->sum('current_amount');
        $totalGoals = (clone $goals)->count();
        $completedGoals = (clone $goals)->where('is_completed', true)->count();
        $activeGoals = $totalGoals - $completedGoals;

        $totalTarget = (clone $goals)->sum('target_amount');

        $overallProgress = $totalTarget > 0
            ? round(($totalSaved / $totalTarget) * 100, 2)
            : 0;

        return [
            'total_saved' => (float) $totalSaved,
            'total_target' => (float) $totalTarget,
            'total_goals' => $totalGoals,
            'active_goals' => $activeGoals,
            'completed_goals' => $completedGoals,
            'overall_progress' => $overallProgress,
        ];
    }

    public function getGoalWithTransactions(int $goalId, int $userId): ?SavingsGoal
    {
        return SavingsGoal::where('user_id', $userId)
            ->with('transactions')
            ->findOrFail($goalId);
    }

    public function getRecentTransactions(int $userId, int $limit = 10): mixed
    {
        return SavingsTransaction::whereHas('goal', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with('goal')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
