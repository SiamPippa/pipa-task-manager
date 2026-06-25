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
        $role = $user->actingRole();

        if ($role === UserRole::ADMIN) {
            return true;
        }

        $permissions = config('rbac.roles.'.$role, []);

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        return self::impliesViewPermission($permissions, $permission);
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

    public static function inSameDepartment(User $user, Model $model): bool
    {
        if (ActiveRole::isAdmin($user)) {
            return true;
        }

        if (! $user->department_id || ! isset($model->department_id)) {
            return false;
        }

        return (int) $model->department_id === (int) $user->department_id;
    }

    public static function canManageOrganization(User $user): bool
    {
        return self::allows($user, Permission::ORGANIZATION_ACCESS);
    }

    public static function roleHasPermission(int $role, string $permission): bool
    {
        if ($role === UserRole::ADMIN) {
            return true;
        }

        $permissions = config('rbac.roles.'.$role, []);

        if (in_array('*', $permissions, true)) {
            return true;
        }

        if (in_array($permission, $permissions, true)) {
            return true;
        }

        return self::impliesViewPermission($permissions, $permission);
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

    private static function impliesViewPermission(array $permissions, string $permission): bool
    {
        if (! str_ends_with($permission, '.view')) {
            return false;
        }

        $resource = substr($permission, 0, -5);

        foreach ($permissions as $granted) {
            if (str_starts_with($granted, $resource.'.')) {
                return true;
            }
        }

        return false;
    }
}
