<?php

namespace App\Policies;

use App\Models\QualityChecklist;
use App\Models\User;

class QualityChecklistPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, QualityChecklist $qualityChecklist): bool
    {
        return $user->id === $qualityChecklist->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, QualityChecklist $qualityChecklist): bool
    {
        return $user->id === $qualityChecklist->user_id;
    }

    public function delete(User $user, QualityChecklist $qualityChecklist): bool
    {
        return $user->id === $qualityChecklist->user_id;
    }
}
