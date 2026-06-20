<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\TaskAssignmentServiceInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Requests\TaskAssignment\StoreTaskAssignmentRequest;
use App\Http\Requests\TaskAssignment\UpdateTaskAssignmentRequest;
use App\Models\TaskAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskAssignmentController extends Controller
{
    public function __construct(
        private readonly TaskAssignmentServiceInterface $taskAssignmentService,
        private readonly CompanyServiceInterface $companyService,
        private readonly ProjectServiceInterface $projectService,
        private readonly TaskServiceInterface $taskService,
        private readonly UserServiceInterface $userService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', TaskAssignment::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'project_id', 'task_id', 'user_id']);
        $filters['viewer_id'] = auth()->id();

        return view('task-assignments.index', [
            'taskAssignments' => $this->taskAssignmentService->paginate($filters, 15, ['task.project', 'user', 'assignedBy']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Task or user', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => 'company_id', 'lookup' => 'projects'],
                ['type' => 'select', 'name' => 'task_id', 'label' => 'Task', 'placeholder' => 'All tasks', 'col' => 2, 'options' => $this->taskService->all(), 'optionLabel' => 'title', 'dependsOn' => 'project_id', 'lookup' => 'tasks'],
                ['type' => 'select', 'name' => 'user_id', 'label' => 'User', 'placeholder' => 'All users', 'col' => 2, 'options' => $this->userService->all(), 'dependsOn' => 'task_id', 'lookup' => 'users'],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', TaskAssignment::class);

        return view('task-assignments.create', [
            'tasks' => $this->scopedForCompany($this->taskService->all(['project'])),
        ]);
    }

    public function store(StoreTaskAssignmentRequest $request): RedirectResponse
    {
        $this->authorize('create', TaskAssignment::class);

        $data = $request->validated();
        $data['assigned_by'] = auth()->id();
        $data['assigned_at'] = now();

        $this->taskAssignmentService->create($data);

        return redirect()->route('task-assignments.index')
            ->with('success', 'Task assignment created successfully.');
    }

    public function show(int $task_assignment): View
    {
        $taskAssignment = $this->taskAssignmentService->findOrFail($task_assignment, ['task', 'user', 'assignedBy']);
        $this->authorize('view', $taskAssignment);

        return view('task-assignments.show', [
            'taskAssignment' => $taskAssignment,
        ]);
    }

    public function edit(int $task_assignment): View
    {
        $taskAssignment = $this->taskAssignmentService->findOrFail($task_assignment);
        $this->authorize('update', $taskAssignment);

        return view('task-assignments.edit', [
            'taskAssignment' => $taskAssignment,
            'tasks' => $this->scopedForCompany($this->taskService->all(['project'])),
        ]);
    }

    public function update(UpdateTaskAssignmentRequest $request, int $task_assignment): RedirectResponse
    {
        $taskAssignment = $this->taskAssignmentService->findOrFail($task_assignment);
        $this->authorize('update', $taskAssignment);

        $this->taskAssignmentService->update($task_assignment, $request->validated());

        return redirect()->route('task-assignments.index')
            ->with('success', 'Task assignment updated successfully.');
    }

    public function destroy(int $task_assignment): RedirectResponse
    {
        $taskAssignment = $this->taskAssignmentService->findOrFail($task_assignment);
        $this->authorize('delete', $taskAssignment);

        $this->taskAssignmentService->delete($task_assignment);

        return redirect()->route('task-assignments.index')
            ->with('success', 'Task assignment deleted successfully.');
    }
}
