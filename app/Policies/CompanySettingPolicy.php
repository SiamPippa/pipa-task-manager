<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\CompanySetting;
use App\Models\User;

class CompanySettingPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->allows($user, Permission::COMPANY_SETTINGS_VIEW);
    }

    public function view(User $user, CompanySetting $companySetting): bool
    {
        return $this->viewAny($user) && $this->sameCompany($user, $companySetting);
    }

    public function create(User $user): bool
    {
        return $this->allows($user, Permission::COMPANY_SETTINGS_MANAGE);
    }

    public function update(User $user, CompanySetting $companySetting): bool
    {
        return $this->create($user) && $this->sameCompany($user, $companySetting);
    }

    public function delete(User $user, CompanySetting $companySetting): bool
    {
        return $this->update($user, $companySetting);
    }
}
