<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Contracts\Services\ProjectModuleServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Http\Requests\ProjectModule\StoreProjectModuleRequest;
use App\Http\Requests\ProjectModule\UpdateProjectModuleRequest;
use App\Models\CompanySetting;
use App\Models\ProjectModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectModuleController extends Controller
{
    public function __construct(
        private readonly ProjectModuleServiceInterface $projectModuleService,
        private readonly CompanyServiceInterface $companyService,
        private readonly DepartmentServiceInterface $departmentService,
        private readonly ProjectServiceInterface $projectService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', ProjectModule::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'department_id', 'project_id']);
        $filters['viewer_id'] = auth()->id();

        return view('project-modules.index', [
            'projectModules' => $this->projectModuleService->paginate($filters, 15, ['project.company', 'project.department']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Module name', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'department_id', 'label' => 'Department', 'placeholder' => 'All departments', 'col' => 2, 'options' => $this->departmentService->all(), 'dependsOn' => 'company_id', 'lookup' => 'departments'],
                ['type' => 'select', 'name' => 'project_id', 'label' => 'Project', 'placeholder' => 'All projects', 'col' => 2, 'options' => $this->projectService->all(), 'dependsOn' => ['company_id', 'department_id'], 'lookup' => 'projects'],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', ProjectModule::class);

        $companies = $this->scopedForCompany($this->companyService->all());

        return view('project-modules.create', [
            'projectModule' => new ProjectModule,
            'companies' => $companies,
            'departments' => $this->scopedForCompany($this->departmentService->all()),
            'projects' => collect(),
            'companyWorkingHours' => $this->companyWorkingHours($companies),
        ]);
    }

    public function store(StoreProjectModuleRequest $request): RedirectResponse
    {
        $this->authorize('create', ProjectModule::class);

        $this->projectModuleService->create($request->validated());

        return redirect()->route('project-modules.index')
            ->with('success', 'Module created successfully.');
    }

    public function show(int $project_module): View
    {
        $module = $this->projectModuleService->findOrFail($project_module, ['project.company', 'project.department']);
        $this->authorize('view', $module);

        return view('project-modules.show', [
            'projectModule' => $module,
        ]);
    }

    public function edit(int $project_module): View
    {
        $module = $this->projectModuleService->findOrFail($project_module, ['project']);
        $this->authorize('update', $module);

        $companies = $this->scopedForCompany($this->companyService->all());

        return view('project-modules.edit', [
            'projectModule' => $module,
            'companies' => $companies,
            'departments' => $this->scopedForCompany($this->departmentService->all()),
            'projects' => $this->scopedForCompany($this->projectService->all()),
            'companyWorkingHours' => $this->companyWorkingHours($companies),
        ]);
    }

    public function update(UpdateProjectModuleRequest $request, int $project_module): RedirectResponse
    {
        $module = $this->projectModuleService->findOrFail($project_module);
        $this->authorize('update', $module);

        $this->projectModuleService->update($project_module, $request->validated());

        return redirect()->route('project-modules.index')
            ->with('success', 'Module updated successfully.');
    }

    public function destroy(int $project_module): RedirectResponse
    {
        $module = $this->projectModuleService->findOrFail($project_module);
        $this->authorize('delete', $module);

        $this->projectModuleService->delete($project_module);

        return redirect()->route('project-modules.index')
            ->with('success', 'Module deleted successfully.');
    }

    private function companyWorkingHours($companies): array
    {
        return CompanySetting::query()
            ->whereIn('company_id', $companies->pluck('id'))
            ->pluck('working_hours_per_day', 'company_id')
            ->all();
    }
}
