<?php

namespace App\Enums;

class UserRole
{
    public const SUPER_ADMIN = 'super-admin';

    public const COMPANY_ADMIN = 'company-admin';

    public const PROJECT_MANAGER = 'project-manager';

    public const TEAM_LEAD = 'team-lead';

    public const DEVELOPER = 'developer';

    public const QA = 'qa';

    public const VIEWER = 'viewer';

    public static function labels(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Admin',
            self::COMPANY_ADMIN => 'Company Admin',
            self::PROJECT_MANAGER => 'Project Manager',
            self::TEAM_LEAD => 'Team Lead',
            self::DEVELOPER => 'Developer',
            self::QA => 'QA',
            self::VIEWER => 'Viewer / Management',
        ];
    }

    public static function label(string|int $role): string
    {
        return self::labels()[self::normalize($role)] ?? 'Unknown';
    }

    public static function values(): array
    {
        return array_keys(self::labels());
    }

    public static function normalize(string|int $role): string
    {
        return match ($role) {
            1 => self::SUPER_ADMIN,
            2 => self::COMPANY_ADMIN,
            3 => self::PROJECT_MANAGER,
            4 => self::TEAM_LEAD,
            5 => self::DEVELOPER,
            default => (string) $role,
        };
    }

    public static function oldRoleMap(): array
    {
        return [
            1 => self::SUPER_ADMIN,
            2 => self::COMPANY_ADMIN,
            3 => self::PROJECT_MANAGER,
            4 => self::TEAM_LEAD,
            5 => self::DEVELOPER,
        ];
    }
}
