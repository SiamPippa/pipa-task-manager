<?php

namespace App\Policies;

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

}
