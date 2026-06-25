<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;

class CompanyPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::COMPANIES_VIEW);
    }

    public function view(User $user, Company $company): bool
    {
        return $this->viewAny($user) && $this->sameCompany($user, $company);
    }

    public function create(User $user): bool
    {
        return $user->actingRole() === UserRole::ADMIN;
    }

    public function update(User $user, Company $company): bool
    {
        return $user->actingRole() === UserRole::ADMIN;
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->actingRole() === UserRole::ADMIN;
    }
}
