<?php

namespace App\Http\Controllers;

use App\Contracts\Services\DailyReportServiceInterface;
use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ProjectModuleServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Requests\DailyReport\StoreDailyReportRequest;
use App\Http\Requests\DailyReport\UpdateDailyReportRequest;
use App\Models\DailyReport;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyReportController extends Controller
{
    public function __construct(
        private readonly DailyReportServiceInterface $dailyReportService,
        private readonly UserServiceInterface $userService,
        private readonly CompanyServiceInterface $companyService,
        private readonly ProjectServiceInterface $projectService,
        private readonly ProjectModuleServiceInterface $projectModuleService,
        private readonly TaskServiceInterface $taskService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', DailyReport::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'user_id', 'project_id', 'project_module_id', 'task_id', 'date_from', 'date_to']);
        $filters['viewer_id'] = auth()->id();

        return view('daily-reports.index', [
            'dailyReports' => $this->dailyReportService->paginate($filters, 15, ['user', 'project', 'module', 'task', 'timeLog']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Summary, blocker, plan', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'user_id', 'label' => 'User', 'placeholder' => 'All users', 'col' => 2, 'options' => $this->userService->all(), 'dependsOn' => 'company_id', 'lookup' => 'users'],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => 'company_id', 'lookup' => 'projects'],
                ['type' => 'select', 'name' => 'project_module_id', 'label' => 'Module', 'placeholder' => 'All modules', 'col' => 2, 'options' => $this->projectModuleService->all(), 'dependsOn' => 'project_id', 'lookup' => 'project-modules'],
                ['type' => 'select', 'name' => 'task_id', 'label' => 'Task', 'placeholder' => 'All tasks', 'col' => 2, 'options' => $this->taskService->all(), 'optionLabel' => 'title', 'dependsOn' => ['project_id', 'project_module_id'], 'lookup' => 'tasks'],
                ['type' => 'date', 'name' => 'date_from', 'label' => 'From', 'col' => 2],
                ['type' => 'date', 'name' => 'date_to', 'label' => 'To', 'col' => 2],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', DailyReport::class);

        return view('daily-reports.create', [
            'projects' => $this->availableProjects(),
            'modules' => $this->availableModules(),
            'tasks' => collect(),
        ]);
    }

    public function store(StoreDailyReportRequest $request): RedirectResponse
    {
        $this->authorize('create', DailyReport::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $this->dailyReportService->create($data);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report created successfully.');
    }

    public function show(int $daily_report): View
    {
        $dailyReport = $this->dailyReportService->findOrFail($daily_report, ['user', 'project', 'module', 'task', 'timeLog']);
        $this->authorize('view', $dailyReport);

        return view('daily-reports.show', [
            'dailyReport' => $dailyReport,
        ]);
    }

    public function edit(int $daily_report): View
    {
        $dailyReport = $this->dailyReportService->findOrFail($daily_report, ['timeLog', 'user']);
        $this->authorize('update', $dailyReport);

        return view('daily-reports.edit', [
            'dailyReport' => $dailyReport,
            'projects' => $this->availableProjects($dailyReport),
            'modules' => $this->availableModules($dailyReport),
            'tasks' => $this->availableTasks($dailyReport),
        ]);
    }

    public function update(UpdateDailyReportRequest $request, int $daily_report): RedirectResponse
    {
        $dailyReport = $this->dailyReportService->findOrFail($daily_report);
        $this->authorize('update', $dailyReport);

        $this->dailyReportService->update($daily_report, $request->validated());

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report updated successfully.');
    }

    public function destroy(int $daily_report): RedirectResponse
    {
        $dailyReport = $this->dailyReportService->findOrFail($daily_report);
        $this->authorize('delete', $dailyReport);

        $this->dailyReportService->delete($daily_report);

        return redirect()->route('daily-reports.index')
            ->with('success', 'Daily report deleted successfully.');
    }

    private function availableProjects(?DailyReport $dailyReport = null)
    {
        $user = auth()->user();

        if (! $user?->company_id) {
            return collect();
        }

        return Project::query()
            ->where('company_id', $user->company_id)
            ->where(function ($query) use ($user, $dailyReport) {
                $query->assignedToUserTeams($user);

                if ($dailyReport?->project_id) {
                    $query->orWhere('id', $dailyReport->project_id);
                }
            })
            ->orderBy('name')
            ->get();
    }

    private function availableModules(?DailyReport $dailyReport = null)
    {
        if (! $dailyReport?->project_id) {
            return collect();
        }

        return ProjectModule::query()
            ->where('project_id', $dailyReport->project_id)
            ->orderBy('name')
            ->get();
    }

    private function availableTasks(?DailyReport $dailyReport = null)
    {
        if (! $dailyReport?->project_id || ! $dailyReport?->project_module_id) {
            return collect();
        }

        return Task::query()
            ->where('project_id', $dailyReport->project_id)
            ->where('project_module_id', $dailyReport->project_module_id)
            ->orderBy('title')
            ->get();
    }
}
