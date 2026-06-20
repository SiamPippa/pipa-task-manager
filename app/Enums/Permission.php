<?php

namespace App\Enums;

class Permission
{
    public const ORGANIZATION_ACCESS = 'organization.access';

    public const COMPANIES_VIEW = 'companies.view';

    public const COMPANIES_MANAGE = 'companies.manage';

    public const COMPANY_SETTINGS_MANAGE = 'company-settings.manage';

    public const DEPARTMENTS_MANAGE = 'departments.manage';

    public const DESIGNATIONS_MANAGE = 'designations.manage';

    public const USERS_MANAGE = 'users.manage';

    public const PROJECTS_VIEW = 'projects.view';

    public const PROJECTS_MANAGE = 'projects.manage';

    public const TEAMS_VIEW = 'teams.view';

    public const TEAMS_MANAGE = 'teams.manage';

    public const TEAMS_MANAGE_LED = 'teams.manage-led';

    public const PROJECT_TEAM_ASSIGNMENTS_VIEW = 'project-team-assignments.view';

    public const PROJECT_TEAM_ASSIGNMENTS_MANAGE = 'project-team-assignments.manage';

    public const TASKS_VIEW = 'tasks.view';

    public const TASKS_MANAGE = 'tasks.manage';

    public const TASK_ASSIGNMENTS_VIEW = 'task-assignments.view';

    public const TASK_ASSIGNMENTS_MANAGE = 'task-assignments.manage';

    public const DAILY_REPORTS_VIEW = 'daily-reports.view';

    public const DAILY_REPORTS_MANAGE = 'daily-reports.manage';

    public const DAILY_REPORTS_MANAGE_OWN = 'daily-reports.manage-own';

    public const RBAC_VIEW = 'rbac.view';

    public static function all(): array
    {
        return array_values((new \ReflectionClass(self::class))->getConstants());
    }

    public static function labels(): array
    {
        return [
            self::ORGANIZATION_ACCESS => 'Organization Access',
            self::COMPANIES_VIEW => 'View Companies',
            self::COMPANIES_MANAGE => 'Manage Companies',
            self::COMPANY_SETTINGS_MANAGE => 'Manage Company Settings',
            self::DEPARTMENTS_MANAGE => 'Manage Departments',
            self::DESIGNATIONS_MANAGE => 'Manage Designations',
            self::USERS_MANAGE => 'Manage Users',
            self::PROJECTS_VIEW => 'View Projects',
            self::PROJECTS_MANAGE => 'Manage Projects',
            self::TEAMS_VIEW => 'View Teams',
            self::TEAMS_MANAGE => 'Manage Teams',
            self::TEAMS_MANAGE_LED => 'Manage Led Teams',
            self::PROJECT_TEAM_ASSIGNMENTS_VIEW => 'View Project Team Assignments',
            self::PROJECT_TEAM_ASSIGNMENTS_MANAGE => 'Manage Project Team Assignments',
            self::TASKS_VIEW => 'View Tasks',
            self::TASKS_MANAGE => 'Manage Tasks',
            self::TASK_ASSIGNMENTS_VIEW => 'View Task Assignments',
            self::TASK_ASSIGNMENTS_MANAGE => 'Manage Task Assignments',
            self::DAILY_REPORTS_VIEW => 'View Daily Reports',
            self::DAILY_REPORTS_MANAGE => 'Manage Daily Reports',
            self::DAILY_REPORTS_MANAGE_OWN => 'Manage Own Daily Reports',
            self::RBAC_VIEW => 'View RBAC Permissions',
        ];
    }

    public static function label(string $permission): string
    {
        return self::labels()[$permission] ?? $permission;
    }

    public static function groups(): array
    {
        return [
            'Organization' => [
                self::ORGANIZATION_ACCESS,
                self::COMPANIES_VIEW,
                self::COMPANIES_MANAGE,
                self::COMPANY_SETTINGS_MANAGE,
                self::DEPARTMENTS_MANAGE,
                self::DESIGNATIONS_MANAGE,
                self::USERS_MANAGE,
            ],
            'Projects & Teams' => [
                self::PROJECTS_VIEW,
                self::PROJECTS_MANAGE,
                self::TEAMS_VIEW,
                self::TEAMS_MANAGE,
                self::TEAMS_MANAGE_LED,
                self::PROJECT_TEAM_ASSIGNMENTS_VIEW,
                self::PROJECT_TEAM_ASSIGNMENTS_MANAGE,
            ],
            'Tasks' => [
                self::TASKS_VIEW,
                self::TASKS_MANAGE,
                self::TASK_ASSIGNMENTS_VIEW,
                self::TASK_ASSIGNMENTS_MANAGE,
            ],
            'Reports' => [
                self::DAILY_REPORTS_VIEW,
                self::DAILY_REPORTS_MANAGE,
                self::DAILY_REPORTS_MANAGE_OWN,
            ],
            'System' => [
                self::RBAC_VIEW,
            ],
        ];
    }
}
