<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

class ActiveRole
{
    public const SESSION_KEY = 'active_role';

    public static function resolve(User $user): int
    {
        $roles = $user->roleIds();

        if ($roles === []) {
            return UserRole::GENERAL;
        }

        $sessionRole = session(self::SESSION_KEY);

        if ($sessionRole !== null && in_array((int) $sessionRole, $roles, true)) {
            return (int) $sessionRole;
        }

        $role = self::defaultFrom($roles);
        session([self::SESSION_KEY => $role]);

        return $role;
    }

    public static function set(User $user, int $role): void
    {
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
        return self::resolve($user) === UserRole::ADMIN;
    }

    /**
     * @param  array<int, int>  $roles
     */
    private static function defaultFrom(array $roles): int
    {
        return min($roles);
    }
}
