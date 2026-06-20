<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Contracts\Services\DesignationServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly CompanyServiceInterface $companyService,
        private readonly DepartmentServiceInterface $departmentService,
        private readonly DesignationServiceInterface $designationService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'department_id', 'designation_id', 'role', 'status']);

        return view('users.index', [
            'users' => $this->userService->paginate($filters, 15, ['company', 'department', 'designation']),
            'filters' => $filters,
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or email', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'department_id', 'label' => 'Department', 'placeholder' => 'All departments', 'col' => 2, 'options' => $this->departmentService->all(), 'dependsOn' => 'company_id', 'lookup' => 'departments'],
                ['type' => 'select', 'name' => 'designation_id', 'label' => 'Designation', 'placeholder' => 'All designations', 'col' => 2, 'options' => $this->designationService->all(), 'optionLabel' => 'title', 'dependsOn' => 'company_id', 'lookup' => 'designations'],
                ['type' => 'select', 'name' => 'role', 'label' => 'Role', 'placeholder' => 'All roles', 'col' => 2, 'options' => FilterOptions::userRoles()],
                ['type' => 'select', 'name' => 'status', 'label' => 'Status', 'placeholder' => 'All statuses', 'col' => 2, 'options' => FilterOptions::booleanStatus()],
            ]),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'departments' => $this->scopedForCompany($this->departmentService->all()),
            'designations' => $this->scopedForCompany($this->designationService->all()),
            'managers' => $this->scopedForCompany($this->userService->all()),
            'roles' => FilterOptions::assignableRoleOptions(auth()->user()),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->userService->create($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(int $user): View
    {
        $userModel = $this->userService->findOrFail($user, [
            'company',
            'department',
            'designation',
            'reportingManager',
        ]);
        $this->authorize('view', $userModel);

        return view('users.show', [
            'user' => $userModel,
        ]);
    }

    public function edit(int $user): View
    {
        $userModel = $this->userService->findOrFail($user);
        $this->authorize('update', $userModel);

        return view('users.edit', [
            'user' => $userModel,
            'companies' => $this->scopedForCompany($this->companyService->all()),
            'departments' => $this->scopedForCompany($this->departmentService->all()),
            'designations' => $this->scopedForCompany($this->designationService->all()),
            'managers' => $this->scopedForCompany($this->userService->all()),
            'roles' => FilterOptions::assignableRoleOptions(auth()->user()),
        ]);
    }

    public function update(UpdateUserRequest $request, int $user): RedirectResponse
    {
        $userModel = $this->userService->findOrFail($user);
        $this->authorize('update', $userModel);

        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        $this->userService->update($user, $data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(int $user): RedirectResponse
    {
        $userModel = $this->userService->findOrFail($user);
        $this->authorize('delete', $userModel);

        $this->userService->delete($user);

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
