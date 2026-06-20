<?php

namespace App\Providers;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Repositories\CompanySettingRepositoryInterface;
use App\Contracts\Repositories\DailyReportRepositoryInterface;
use App\Contracts\Repositories\DepartmentRepositoryInterface;
use App\Contracts\Repositories\DesignationRepositoryInterface;
use App\Contracts\Repositories\ProjectTeamAssignmentRepositoryInterface;
use App\Contracts\Repositories\ProjectAnalyticsRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\TaskAssignmentRepositoryInterface;
use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\TimeLogRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\CompanySettingServiceInterface;
use App\Contracts\Services\DailyReportServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Contracts\Services\DesignationServiceInterface;
use App\Contracts\Services\ProjectTeamAssignmentServiceInterface;
use App\Contracts\Services\ProjectAnalyticsServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\TaskAssignmentServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Contracts\Services\TimeLogServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanySettingRepository;
use App\Repositories\DailyReportRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\DesignationRepository;
use App\Repositories\ProjectTeamAssignmentRepository;
use App\Repositories\ProjectAnalyticsRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskAssignmentRepository;
use App\Repositories\TaskRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TimeLogRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\CompanyService;
use App\Services\CompanySettingService;
use App\Services\DailyReportService;
use App\Services\DepartmentService;
use App\Services\DesignationService;
use App\Services\ProjectTeamAssignmentService;
use App\Services\ProjectAnalyticsService;
use App\Services\ProjectService;
use App\Services\TaskAssignmentService;
use App\Services\TaskService;
use App\Services\TeamService;
use App\Services\TimeLogService;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $bindings = [
            UserRepositoryInterface::class => UserRepository::class,
            CompanyRepositoryInterface::class => CompanyRepository::class,
            CompanySettingRepositoryInterface::class => CompanySettingRepository::class,
            DepartmentRepositoryInterface::class => DepartmentRepository::class,
            TeamRepositoryInterface::class => TeamRepository::class,
            DesignationRepositoryInterface::class => DesignationRepository::class,
            ProjectRepositoryInterface::class => ProjectRepository::class,
            ProjectAnalyticsRepositoryInterface::class => ProjectAnalyticsRepository::class,
            TaskRepositoryInterface::class => TaskRepository::class,
            TaskAssignmentRepositoryInterface::class => TaskAssignmentRepository::class,
            ProjectTeamAssignmentRepositoryInterface::class => ProjectTeamAssignmentRepository::class,
            TimeLogRepositoryInterface::class => TimeLogRepository::class,
            DailyReportRepositoryInterface::class => DailyReportRepository::class,

            AuthServiceInterface::class => AuthService::class,
            UserServiceInterface::class => UserService::class,
            CompanyServiceInterface::class => CompanyService::class,
            CompanySettingServiceInterface::class => CompanySettingService::class,
            DepartmentServiceInterface::class => DepartmentService::class,
            TeamServiceInterface::class => TeamService::class,
            DesignationServiceInterface::class => DesignationService::class,
            ProjectServiceInterface::class => ProjectService::class,
            ProjectAnalyticsServiceInterface::class => ProjectAnalyticsService::class,
            TaskServiceInterface::class => TaskService::class,
            TaskAssignmentServiceInterface::class => TaskAssignmentService::class,
            ProjectTeamAssignmentServiceInterface::class => ProjectTeamAssignmentService::class,
            TimeLogServiceInterface::class => TimeLogService::class,
            DailyReportServiceInterface::class => DailyReportService::class,
        ];

        foreach ($bindings as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.bootstrap-5');

        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->company_id && ! auth()->user()->relationLoaded('company')) {
                auth()->user()->load('company');
            }
        });
    }
}
