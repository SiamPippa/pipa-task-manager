<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\OfficeLocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectAnalyticsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectModuleController;
use App\Http\Controllers\ProjectTeamAssignmentController;
use App\Http\Controllers\SwitchActiveRoleController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProjectAnalyticsController::class, 'index'])->name('dashboard');
    Route::get('/analytics/projects/{project}', [ProjectAnalyticsController::class, 'show'])->name('analytics.projects.show');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/active-role', SwitchActiveRoleController::class)->name('active-role.switch');
    Route::get('/lookup/{type}', LookupController::class)->name('lookup');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{role}', [PermissionController::class, 'show'])->name('permissions.show');

    Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user-roles.index');
    Route::patch('/user-roles/{user}', [UserRoleController::class, 'update'])->name('user-roles.update');

    Route::resource('companies', CompanyController::class);
    Route::resource('company-settings', CompanySettingController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('office-locations', OfficeLocationController::class);
    Route::resource('users', UserController::class);
    Route::resource('teams', TeamController::class);
    Route::resource('project-modules', ProjectModuleController::class);
    Route::resource('project-team-assignments', ProjectTeamAssignmentController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('project-tasks', TaskController::class);
    Route::patch('/task-assignments/{task_assignment}/task-status', [TaskAssignmentController::class, 'updateTaskStatus'])
        ->name('task-assignments.task-status.update');
    Route::resource('task-assignments', TaskAssignmentController::class);
    Route::resource('time-logs', TimeLogController::class);
    Route::resource('daily-reports', DailyReportController::class);
});
