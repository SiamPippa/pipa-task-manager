<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class CompanyContext
{
    public static function canSelectCompany(?User $user = null): bool
    {
        $user ??= auth()->user();

        return ActiveRole::isAdmin($user);
    }

    public static function companyId(?User $user = null): ?int
    {
        $user ??= auth()->user();

        return $user?->company_id;
    }

    public static function applyFilters(array $filters, ?User $user = null): array
    {
        if (self::canSelectCompany($user)) {
            return $filters;
        }

        if ($companyId = self::companyId($user)) {
            $filters['company_id'] = $companyId;
        }

        if (! self::canSelectDepartment($user) && ($departmentId = self::departmentId($user))) {
            $filters['department_id'] = $departmentId;
        }

        return $filters;
    }

    public static function withoutCompanyFilter(array $fields, ?User $user = null): array
    {
        if (self::canSelectCompany($user)) {
            return $fields;
        }

        $hidden = ['company_id'];

        if (! self::canSelectDepartment($user)) {
            $hidden[] = 'department_id';
        }

        return array_values(array_filter(
            $fields,
            fn (array $field) => ! in_array($field['name'] ?? null, $hidden, true)
        ));
    }

    public static function resolveCompanyId(?int $selected = null, ?User $user = null): ?int
    {
        if (self::canSelectCompany($user)) {
            return $selected;
        }

        return self::companyId($user) ?? $selected;
    }

    public static function canSelectDepartment(?User $user = null): bool
    {
        $user ??= auth()->user();

        return self::canSelectCompany($user) || self::hasCompanyWideDepartmentSelection($user);
    }

    public static function departmentId(?User $user = null): ?int
    {
        $user ??= auth()->user();

        return $user?->department_id;
    }

    public static function resolveDepartmentId(?int $selected = null, ?User $user = null): ?int
    {
        if (self::canSelectDepartment($user)) {
            return $selected;
        }

        if ($selected !== null) {
            return $selected;
        }

        return self::departmentId($user);
    }

    public static function filterByCompany(Collection|EloquentCollection $items, ?User $user = null): Collection|EloquentCollection
    {
        if (self::canSelectCompany($user)) {
            return $items;
        }

        $companyId = self::companyId($user);

        if (! $companyId) {
            return $items instanceof EloquentCollection
                ? new EloquentCollection
                : collect();
        }

        return $items->filter(function ($item) use ($companyId) {
            $itemCompanyId = $item->company_id ?? null;

            if ($itemCompanyId === null && isset($item->project)) {
                $itemCompanyId = $item->project?->company_id;
            }

            return $itemCompanyId !== null && (int) $itemCompanyId === (int) $companyId;
        })->values();
    }

    private static function hasCompanyWideDepartmentSelection(?User $user = null): bool
    {
        $user ??= auth()->user();

        return in_array($user?->actingRole(), [UserRole::MANAGER, UserRole::DEPARTMENT_HEAD], true);
    }
}
