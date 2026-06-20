<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Designation;
use App\Models\User;

class DesignationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public function view(User $user, Designation $designation): bool
    {
        return $this->viewAny($user) && $this->sameCompany($user, $designation);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::DESIGNATIONS_MANAGE);
    }

    public function update(User $user, Designation $designation): bool
    {
        return $this->create($user) && $this->sameCompany($user, $designation);
    }

    public function delete(User $user, Designation $designation): bool
    {
        return $this->update($user, $designation);
    }
}
