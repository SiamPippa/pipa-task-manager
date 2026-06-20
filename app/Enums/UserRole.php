<?php

namespace App\Enums;

class UserRole
{
    public const ADMIN = 1;

    public const DEPARTMENT_HEAD = 2;

    public const TEAM_PRODUCT_MANAGER = 3;

    public const TEAM_LEAD = 4;

    public const GENERAL = 5;

    public static function labels(): array
    {
        return [
            self::ADMIN => 'Admin',
            self::DEPARTMENT_HEAD => 'Department Head',
            self::TEAM_PRODUCT_MANAGER => 'Team Product Manager',
            self::TEAM_LEAD => 'Team Lead',
            self::GENERAL => 'General User',
        ];
    }

    public static function label(int $role): string
    {
        return self::labels()[$role] ?? 'Unknown';
    }

    public static function values(): array
    {
        return array_keys(self::labels());
    }
}
