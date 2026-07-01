<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permission::all() as $permission) {
            SpatiePermission::findOrCreate($permission);
        }

        foreach (UserRole::labels() as $role => $label) {
            Role::findOrCreate($role);
        }

        Role::findByName(UserRole::SUPER_ADMIN)->syncPermissions(Permission::all());

        Role::findByName(UserRole::COMPANY_ADMIN)->syncPermissions([
            Permission::ORGANIZATION_ACCESS,
            Permission::COMPANIES_VIEW,
            Permission::COMPANY_SETTINGS_VIEW,
            Permission::COMPANY_SETTINGS_MANAGE,
            Permission::DESIGNATIONS_MANAGE,
            Permission::OFFICE_LOCATIONS_MANAGE,
            Permission::USERS_MANAGE,
            Permission::PROJECTS_VIEW,
            Permission::PROJECTS_MANAGE,
            Permission::TEAMS_VIEW,
            Permission::TEAMS_MANAGE,
            Permission::PROJECT_TEAM_ASSIGNMENTS_VIEW,
            Permission::PROJECT_TEAM_ASSIGNMENTS_MANAGE,
            Permission::TASKS_VIEW,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_VIEW,
            Permission::DAILY_REPORTS_MANAGE,
            Permission::TIME_LOGS_VIEW,
            Permission::TIME_LOGS_MANAGE,
            Permission::DASHBOARD_VIEW,
            Permission::REPORTS_EXPORT,
            Permission::RBAC_VIEW,
        ]);

        Role::findByName(UserRole::PROJECT_MANAGER)->syncPermissions([
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::PROJECT_TEAM_ASSIGNMENTS_VIEW,
            Permission::TASKS_VIEW,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_VIEW,
            Permission::TIME_LOGS_VIEW,
            Permission::DASHBOARD_VIEW,
            Permission::REPORTS_EXPORT,
        ]);

        Role::findByName(UserRole::TEAM_LEAD)->syncPermissions([
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TEAMS_MANAGE_LED,
            Permission::PROJECT_TEAM_ASSIGNMENTS_VIEW,
            Permission::TASKS_VIEW,
            Permission::TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::TASK_ASSIGNMENTS_MANAGE,
            Permission::DAILY_REPORTS_VIEW,
            Permission::TIME_LOGS_VIEW,
            Permission::DASHBOARD_VIEW,
        ]);

        Role::findByName(UserRole::DEVELOPER)->syncPermissions([
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TASKS_VIEW,
            Permission::TASKS_UPDATE_OWN,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::DAILY_REPORTS_MANAGE_OWN,
            Permission::TIME_LOGS_MANAGE_OWN,
            Permission::DASHBOARD_VIEW,
        ]);

        Role::findByName(UserRole::QA)->syncPermissions([
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TASKS_VIEW,
            Permission::QA_TASKS_VIEW,
            Permission::QA_TASKS_MANAGE,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::DAILY_REPORTS_MANAGE_OWN,
            Permission::TIME_LOGS_MANAGE_OWN,
            Permission::DASHBOARD_VIEW,
        ]);

        Role::findByName(UserRole::VIEWER)->syncPermissions([
            Permission::COMPANIES_VIEW,
            Permission::PROJECTS_VIEW,
            Permission::TEAMS_VIEW,
            Permission::TASKS_VIEW,
            Permission::TASK_ASSIGNMENTS_VIEW,
            Permission::DAILY_REPORTS_VIEW,
            Permission::TIME_LOGS_VIEW,
            Permission::DASHBOARD_VIEW,
            Permission::REPORTS_EXPORT,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
