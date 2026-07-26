<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\SavingsGoal;
use Illuminate\Auth\Access\HandlesAuthorization;

class SavingsGoalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SavingsGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SavingsGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function delete(User $user, SavingsGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function deposit(User $user, SavingsGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }

    public function withdraw(User $user, SavingsGoal $goal): bool
    {
        return $user->id === $goal->user_id;
    }
}
