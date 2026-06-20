<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskServiceInterface $taskService,
        private readonly ProjectServiceInterface $projectService,
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'project_id', 'status']);
        $filters['viewer_id'] = auth()->id();

        return view('tasks.index', [
            'tasks' => $this->taskService->paginate($filters, 15, ['project']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Title or Jira no', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => 'company_id', 'lookup' => 'projects'],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::taskStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'projects' => $this->scopedForCompany($this->projectService->all()),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->authorize('create', Task::class);

        $this->taskService->create($request->validated());

        return redirect()->route('project-tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function show(int $task): View
    {
        $task = $this->taskService->findOrFail($task, ['project']);
        $this->authorize('view', $task);

        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    public function edit(int $task): View
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('update', $taskModel);

        return view('tasks.edit', [
            'task' => $taskModel,
            'projects' => $this->scopedForCompany($this->projectService->all()),
        ]);
    }

    public function update(UpdateTaskRequest $request, int $task): RedirectResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('update', $taskModel);

        $this->taskService->update($task, $request->validated());

        return redirect()->route('project-tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(int $task): RedirectResponse
    {
        $taskModel = $this->taskService->findOrFail($task);
        $this->authorize('delete', $taskModel);

        $this->taskService->delete($task);

        return redirect()->route('project-tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}
