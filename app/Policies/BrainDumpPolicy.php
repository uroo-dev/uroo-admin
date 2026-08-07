<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BrainDump;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrainDumpPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BrainDump $brainDump): bool
    {
        return $user->id === $brainDump->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BrainDump $brainDump): bool
    {
        return $user->id === $brainDump->user_id;
    }

    public function delete(User $user, BrainDump $brainDump): bool
    {
        return $user->id === $brainDump->user_id;
    }

    public function restore(User $user, BrainDump $brainDump): bool
    {
        return $user->id === $brainDump->user_id;
    }

    public function forceDelete(User $user, BrainDump $brainDump): bool
    {
        return $user->id === $brainDump->user_id;
    }
}
