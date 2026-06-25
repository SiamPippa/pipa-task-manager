<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectTeamAssignment;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use App\Support\CompanyContext;
use App\Support\ProjectEstimatedHoursCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function __invoke(Request $request, string $type): JsonResponse
    {
        $items = match ($type) {
            'companies' => $this->companies($request),
            'departments' => $this->departments($request),
            'teams' => $this->teams($request),
            'team-leads' => $this->teamLeads($request),
            'designations' => $this->designations($request),
            'users' => $this->users($request),
            'projects' => $this->projects($request),
            'project-modules' => $this->projectModules($request),
            'project-module-context' => $this->projectModuleContext($request),
            'tasks' => $this->tasks($request),
            default => abort(404),
        };

        return response()->json($items);
    }

    private function companies(Request $request): array
    {
        $query = Company::query()->active();

        if ($viewer = auth()->user()) {
            if (! CompanyContext::canSelectCompany($viewer)) {
                $companyId = CompanyContext::companyId($viewer);

                if (! $companyId) {
                    return [];
                }

                $query->where('id', $companyId);
            }
        }

        return $query
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Company $company) => [
                'id' => $company->id,
                'label' => $company->name,
            ])
            ->all();
    }

    private function departments(Request $request): array
    {
        $companyId = $this->resolveCompanyId($request);

        if (! $companyId) {
            return [];
        }

        return Department::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Department $department) => [
                'id' => $department->id,
                'label' => $department->name,
            ])
            ->all();
    }

    private function teams(Request $request): array
    {
        $query = Team::query();

        if ($viewer = auth()->user()) {
            $query->visibleTo($viewer);
        }

        if ($request->filled('project_id')) {
            $project = Project::query()->find($request->integer('project_id'));

            if (! $project) {
                return [];
            }

            $query
                ->where('company_id', $project->company_id)
                ->where('department_id', $project->department_id);
        } elseif ($companyId = $this->resolveCompanyId($request)) {
            $query->where('company_id', $companyId);

            if ($request->filled('department_id')) {
                $query->where('department_id', $request->integer('department_id'));
            }
        } else {
            return [];
        }

        return $query
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Team $team) => [
                'id' => $team->id,
                'label' => $team->name,
            ])
            ->all();
    }

    private function teamLeads(Request $request): array
    {
        $query = Team::query()->whereNotNull('team_lead_id');

        if ($viewer = auth()->user()) {
            $query->visibleTo($viewer);
        }

        if ($companyId = $this->resolveCompanyId($request)) {
            $query->where('company_id', $companyId);

            if ($request->filled('department_id')) {
                $query->where('department_id', $request->integer('department_id'));
            }
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        } else {
            return [];
        }

        $leadIds = $query->pluck('team_lead_id')->unique()->filter();

        if ($leadIds->isEmpty()) {
            return [];
        }

        return User::query()
            ->whereIn('id', $leadIds)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'label' => $user->name,
            ])
            ->all();
    }

    private function designations(Request $request): array
    {
        $companyId = $this->resolveCompanyId($request);

        if (! $companyId) {
            return [];
        }

        return Designation::query()
            ->where('company_id', $companyId)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Designation $designation) => [
                'id' => $designation->id,
                'label' => $designation->title,
            ])
            ->all();
    }

    private function users(Request $request): array
    {
        $query = User::query()->orderBy('name');

        if ($request->filled('task_id')) {
            $task = Task::query()->with('project')->find($request->integer('task_id'));

            if (! $task?->project) {
                return [];
            }

            $query->where('company_id', $task->project->company_id);
            $this->scopeUsersToProjectMembers($query, $task->project_id);
        } elseif ($request->filled('project_id')) {
            $project = Project::query()->find($request->integer('project_id'));

            if (! $project) {
                return [];
            }

            $query->where('company_id', $project->company_id);
            $this->scopeUsersToProjectMembers($query, $project->id);
        } else {
            if ($companyId = $this->resolveCompanyId($request)) {
                $query->where('company_id', $companyId);
            } elseif ($request->filled('company_id')) {
                $query->where('company_id', $request->integer('company_id'));
            }

            if ($request->filled('department_id')) {
                $query->where('department_id', $request->integer('department_id'));
            }

            if (
                ! $this->resolveCompanyId($request)
                && ! $request->filled('company_id')
                && ! $request->filled('department_id')
            ) {
                return [];
            }
        }

        if ($request->filled('exclude_id')) {
            $query->where('id', '!=', $request->integer('exclude_id'));
        }

        return $query
            ->get(['id', 'name'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'label' => $user->name,
            ])
            ->all();
    }

    private function scopeUsersToProjectMembers($query, int $projectId): void
    {
        $teamIds = ProjectTeamAssignment::query()
            ->where('project_id', $projectId)
            ->pluck('team_id');

        if ($teamIds->isEmpty()) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereHas('teams', function ($teamQuery) use ($teamIds) {
            $teamQuery->whereIn('teams.id', $teamIds);
        });
    }

    private function projects(Request $request): array
    {
        $companyId = $this->resolveCompanyId($request);

        if (! $companyId) {
            return [];
        }

        $query = Project::query()
            ->where('company_id', $companyId);

        if ($viewer = auth()->user()) {
            $query->visibleTo($viewer);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        return $query
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'label' => $project->name,
            ])
            ->all();
    }

    private function projectModules(Request $request): array
    {
        if (! $request->filled('project_id')) {
            return [];
        }

        $query = ProjectModule::query()
            ->where('project_id', $request->integer('project_id'));

        if ($viewer = auth()->user()) {
            $query->visibleTo($viewer);
        }

        return $query
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (ProjectModule $module) => [
                'id' => $module->id,
                'label' => $module->name,
            ])
            ->all();
    }

    private function projectModuleContext(Request $request): array
    {
        if (! $request->filled('project_id')) {
            return [];
        }

        $project = Project::query()
            ->with('company.settings')
            ->find($request->integer('project_id'));

        if (! $project) {
            return [];
        }

        if ($viewer = auth()->user()) {
            if (! $project->isVisibleTo($viewer)) {
                return [];
            }
        }

        return [
            'start_date' => $project->start_date?->toDateString(),
            'end_date' => $project->end_date?->toDateString(),
            'estimated_hours' => $project->estimated_hours !== null
                ? (float) $project->estimated_hours
                : null,
            'hours_per_day' => ProjectEstimatedHoursCalculator::resolveHoursPerDay($project->company?->settings),
        ];
    }

    private function tasks(Request $request): array
    {
        if (! $request->filled('project_id')) {
            return [];
        }

        $query = Task::query()
            ->where('project_id', $request->integer('project_id'));

        if ($request->filled('project_module_id')) {
            $query->where('project_module_id', $request->integer('project_module_id'));
        }

        if ($viewer = auth()->user()) {
            $query->visibleTo($viewer);
        }

        return $query
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'label' => $task->title,
            ])
            ->all();
    }

    private function resolveCompanyId(Request $request): ?int
    {
        if ($request->filled('company_id')) {
            return $request->integer('company_id');
        }

        return CompanyContext::companyId();
    }
}
