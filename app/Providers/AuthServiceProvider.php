<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\DailyReport;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
use App\Models\ProjectTeamAssignment;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\Team;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\CompanySettingPolicy;
use App\Policies\DailyReportPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\DesignationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectTeamAssignmentPolicy;
use App\Policies\TaskAssignmentPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use App\Support\Rbac;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        CompanySetting::class => CompanySettingPolicy::class,
        Department::class => DepartmentPolicy::class,
        Designation::class => DesignationPolicy::class,
        User::class => UserPolicy::class,
        Project::class => ProjectPolicy::class,
        Team::class => TeamPolicy::class,
        ProjectTeamAssignment::class => ProjectTeamAssignmentPolicy::class,
        Task::class => TaskPolicy::class,
        TaskAssignment::class => TaskAssignmentPolicy::class,
        DailyReport::class => DailyReportPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('view-rbac', fn (User $user) => Rbac::allows($user, Permission::RBAC_VIEW));
        Gate::define('assign-user-roles', fn (User $user) => $user->role === UserRole::ADMIN);
    }
}
