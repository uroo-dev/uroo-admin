<?php

declare(strict_types=1);

namespace Modules\Ideas\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Ideas\Models\AppIdea;

class IdeaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AppIdea $idea): bool
    {
        return $user->id === $idea->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AppIdea $idea): bool
    {
        return $user->id === $idea->user_id;
    }

    public function delete(User $user, AppIdea $idea): bool
    {
        return $user->id === $idea->user_id;
    }

    public function restore(User $user, AppIdea $idea): bool
    {
        return $user->id === $idea->user_id;
    }

    public function forceDelete(User $user, AppIdea $idea): bool
    {
        return $user->id === $idea->user_id;
    }
}
