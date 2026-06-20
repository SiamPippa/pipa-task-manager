<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectServiceInterface $projectService,
        private readonly CompanyServiceInterface $companyService,
        private readonly DepartmentServiceInterface $departmentService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Project::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'department_id', 'status']);
        $filters['viewer_id'] = auth()->id();

        return view('projects.index', [
            'projects' => $this->projectService->paginate($filters, 15, ['company', 'department']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'department_id', 'label' => 'Department', 'placeholder' => 'All departments', 'col' => 3, 'options' => $this->departmentService->all(), 'dependsOn' => 'company_id', 'lookup' => 'departments'],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::projectStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('projects.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'departments' => $this->scopedForCompany($this->departmentService->all()),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $this->projectService->create($request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function show(int $project): View
    {
        $project = $this->projectService->findOrFail($project, ['company', 'department']);
        $this->authorize('view', $project);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    public function edit(int $project): View
    {
        $projectModel = $this->projectService->findOrFail($project);
        $this->authorize('update', $projectModel);

        return view('projects.edit', [
            'project' => $projectModel,
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'departments' => $this->scopedForCompany($this->departmentService->all()),
        ]);
    }

    public function update(UpdateProjectRequest $request, int $project): RedirectResponse
    {
        $projectModel = $this->projectService->findOrFail($project);
        $this->authorize('update', $projectModel);

        $this->projectService->update($project, $request->validated());

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(int $project): RedirectResponse
    {
        $projectModel = $this->projectService->findOrFail($project);
        $this->authorize('delete', $projectModel);

        $this->projectService->delete($project);

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
