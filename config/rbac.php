<?php

use App\Enums\Permission;
use App\Enums\UserRole;

return [
    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | Static permissions granted to each role. Resource-level scoping
    | (company, department, team membership) is enforced in policies
    | and model visibleTo scopes.
    |
    */
    'roles' => [
        UserRole::ADMIN => ['*'],

        UserRole::DEPARTMENT_HEAD => [
            Permission::ORGANIZATION_ACCESS,
            Permission::RBAC_VIEW,
            Permission::COMPANIES_VIEW,
            Permission::COMPANY_SETTINGS_VIEW,
            Permission::DEPARTMENTS_MANAGE,
            Permission::DESIGNATIONS_MANAGE,
            Permission::USERS_MANAGE,
            Permission::PROJECTS_MANAGE,
            Permission::TEAMS_MANAGE,
            Permission::PROJECT_TEAM_ASSIGNMENTS_MANAGE,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_MANAGE,
        ],

        UserRole::MANAGER => [
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::PROJECT_TEAM_ASSIGNMENTS_MANAGE,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_MANAGE,
        ],

        UserRole::TEAM_LEAD => [
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TEAMS_MANAGE_LED,
            Permission::PROJECT_TEAM_ASSIGNMENTS_VIEW,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_MANAGE,
        ],

        UserRole::GENERAL => [
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TASKS_VIEW,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::DAILY_REPORTS_MANAGE_OWN,
        ],
    ],
];
