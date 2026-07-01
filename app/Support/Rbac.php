<?php

namespace App\Support;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Rbac
{
    public static function allows(User $user, string $permission): bool
    {
        return $user->actingCan($permission);
    }

    public static function inSameCompany(User $user, Model $model): bool
    {
        if (ActiveRole::isAdmin($user)) {
            return true;
        }

        $companyId = $model instanceof Company
            ? $model->id
            : ($model->company_id ?? null);

        return $companyId !== null && (int) $companyId === (int) $user->company_id;
    }

    public static function canManageOrganization(User $user): bool
    {
        return self::allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public static function roleHasPermission(string|int $role, string $permission): bool
    {
        $role = UserRole::normalize($role);

        if ($role === UserRole::SUPER_ADMIN) {
            return true;
        }

        $spatieRole = \Spatie\Permission\Models\Role::query()
            ->with('permissions')
            ->where('name', $role)
            ->first();

        if ($spatieRole?->hasPermissionTo($permission)) {
            return true;
        }

        return $spatieRole !== null && str_ends_with($permission, '.view')
            && $spatieRole->permissions->contains(
                fn ($granted) => str_starts_with($granted->name, substr($permission, 0, -5).'.')
            );
    }

    /**
     * @return array<int, string>
     */
    public static function roleLabels(): array
    {
        return UserRole::labels();
    }

    /**
     * @return array<int, array{label: string, permissions: array<string, bool>}>
     */
    public static function matrix(): array
    {
        $matrix = [];

        foreach (UserRole::labels() as $roleId => $roleLabel) {
            $permissions = [];

            foreach (Permission::all() as $permission) {
                if ($permission === Permission::RBAC_VIEW) {
                    continue;
                }

                $permissions[$permission] = self::roleHasPermission($roleId, $permission);
            }

            $matrix[$roleId] = [
                'label' => $roleLabel,
                'permissions' => $permissions,
            ];
        }

        return $matrix;
    }

}
