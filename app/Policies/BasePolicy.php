<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\Rbac;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    protected function allows(User $user, string $permission): bool
    {
        return Rbac::allows($user, $permission);
    }

    protected function sameCompany(User $user, Model $model): bool
    {
        return Rbac::inSameCompany($user, $model);
    }

    protected function sameDepartment(User $user, Model $model): bool
    {
        return Rbac::inSameDepartment($user, $model);
    }

    protected function canMutateInOwnDepartment(User $user, Model $model): bool
    {
        if ($user->actingRole() === UserRole::ADMIN) {
            return true;
        }

        if ($user->actingRole() === UserRole::MANAGER) {
            return $this->sameCompany($user, $model) && $this->sameDepartment($user, $model);
        }

        return false;
    }
}
