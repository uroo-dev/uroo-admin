<?php

namespace Modules\Credential\Policies;

use App\Models\User;
use Modules\Credential\Models\Credential;

class CredentialPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Credential $credential): bool
    {
        return $user->id === $credential->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Credential $credential): bool
    {
        return $user->id === $credential->user_id;
    }

    public function delete(User $user, Credential $credential): bool
    {
        return $user->id === $credential->user_id;
    }
}