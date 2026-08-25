<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OwnedModelPolicy
{
    use HandlesAuthorization;

    protected function owns(User $user, $model): bool
    {
        return $user->id === ($model->user_id ?? null);
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, $model): bool
    {
        return $this->owns($user, $model);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, $model): bool
    {
        return $this->owns($user, $model);
    }

    public function delete(User $user, $model): bool
    {
        return $this->owns($user, $model);
    }

    public function restore(User $user, $model): bool
    {
        return $this->owns($user, $model);
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->owns($user, $model);
    }
}
