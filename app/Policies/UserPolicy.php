<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\User;

class UserPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $this->viewAny($user) && $this->sameCompany($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::USERS_MANAGE);
    }

    public function update(User $user, User $model): bool
    {
        if (! $this->create($user) || ! $this->sameCompany($user, $model)) {
            return false;
        }

        if ($user->actingRole() === UserRole::COMPANY_ADMIN && $model->hasRole(UserRole::SUPER_ADMIN)) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        return $this->update($user, $model);
    }
}
