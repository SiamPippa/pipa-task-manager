<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Department;
use App\Models\User;

class DepartmentPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public function view(User $user, Department $department): bool
    {
        return $this->viewAny($user) && $this->sameCompany($user, $department);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::DEPARTMENTS_MANAGE);
    }

    public function update(User $user, Department $department): bool
    {
        return $this->create($user) && $this->sameCompany($user, $department);
    }

    public function delete(User $user, Department $department): bool
    {
        return $this->update($user, $department);
    }
}
