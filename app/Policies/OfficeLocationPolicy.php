<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\OfficeLocation;
use App\Models\User;

class OfficeLocationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public function view(User $user, OfficeLocation $officeLocation): bool
    {
        return $this->viewAny($user) && $this->sameCompany($user, $officeLocation);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::OFFICE_LOCATIONS_MANAGE);
    }

    public function update(User $user, OfficeLocation $officeLocation): bool
    {
        return $this->create($user) && $this->sameCompany($user, $officeLocation);
    }

    public function delete(User $user, OfficeLocation $officeLocation): bool
    {
        return $this->update($user, $officeLocation);
    }
}
