<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

class ActiveRole
{
    public const SESSION_KEY = 'active_role';

    public static function resolve(User $user): string
    {
        $roles = $user->roleIds();

        if ($roles === []) {
            return UserRole::DEVELOPER;
        }

        $sessionRole = session(self::SESSION_KEY);

        if ($sessionRole !== null && in_array(UserRole::normalize($sessionRole), $roles, true)) {
            return UserRole::normalize($sessionRole);
        }

        $role = self::defaultFrom($roles);
        session([self::SESSION_KEY => $role]);

        return $role;
    }

    public static function set(User $user, string|int $role): void
    {
        $role = UserRole::normalize($role);

        if (! in_array($role, $user->roleIds(), true)) {
            abort(403);
        }

        session([self::SESSION_KEY => $role]);
    }

    public static function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public static function isAdmin(User $user): bool
    {
        return self::resolve($user) === UserRole::SUPER_ADMIN;
    }

    /**
     * @param  array<int, string>  $roles
     */
    public static function defaultFrom(array $roles): string
    {
        $priority = [
            UserRole::SUPER_ADMIN,
            UserRole::COMPANY_ADMIN,
            UserRole::PROJECT_MANAGER,
            UserRole::TEAM_LEAD,
            UserRole::QA,
            UserRole::DEVELOPER,
            UserRole::VIEWER,
        ];

        foreach ($priority as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return $roles[0] ?? UserRole::DEVELOPER;
    }
}
