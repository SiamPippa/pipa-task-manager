<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Models\Department;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly DepartmentServiceInterface $departmentService,
        private readonly CompanyServiceInterface $companyService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Department::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'status']);

        return view('departments.index', [
            'departments' => $this->departmentService->paginate($filters, 15, ['company']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or code', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 3, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::booleanStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Department::class);

        return view('departments.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
        ]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $this->authorize('create', Department::class);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->departmentService->create($data);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(int $department): View
    {
        $department = $this->departmentService->findOrFail($department, ['company']);
        $this->authorize('view', $department);

        return view('departments.show', [
            'department' => $department,
        ]);
    }

    public function edit(int $department): View
    {
        $departmentModel = $this->departmentService->findOrFail($department);
        $this->authorize('update', $departmentModel);

        return view('departments.edit', [
            'department' => $departmentModel,
            'companies' => $this->scopedForCompany($this->companyService->all()),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, int $department): RedirectResponse
    {
        $departmentModel = $this->departmentService->findOrFail($department);
        $this->authorize('update', $departmentModel);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->departmentService->update($department, $data);

        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(int $department): RedirectResponse
    {
        $departmentModel = $this->departmentService->findOrFail($department);
        $this->authorize('delete', $departmentModel);

        $this->departmentService->delete($department);

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
