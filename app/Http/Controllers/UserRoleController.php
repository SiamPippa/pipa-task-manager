<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CompanyServiceInterface;
use App\Contracts\Services\DepartmentServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\UserRole;
use App\Http\Requests\User\AssignUserRoleRequest;
use App\Support\FilterOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly CompanyServiceInterface $companyService,
        private readonly DepartmentServiceInterface $departmentService
    ) {}

    public function index(Request $request): View
    {
        Gate::authorize('assign-user-roles');

        $filters = $this->scopedFilters($request, ['search', 'company_id', 'department_id', 'role']);

        return view('user-roles.index', [
            'users' => $this->userService->paginate($filters, 15, ['company', 'department', 'userRoles']),
            'filters' => $filters,
            'roles' => UserRole::labels(),
            'filterFields' => $this->scopedFilterFields([
                ['type' => 'text', 'name' => 'search', 'label' => 'Search', 'placeholder' => 'Name or email', 'col' => 3],
                ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'placeholder' => 'All companies', 'col' => 2, 'options' => $this->companyService->all()],
                ['type' => 'select', 'name' => 'department_id', 'label' => 'Department', 'placeholder' => 'All departments', 'col' => 2, 'options' => $this->departmentService->all(), 'dependsOn' => 'company_id', 'lookup' => 'departments'],
                ['type' => 'select', 'name' => 'role', 'label' => 'Role', 'placeholder' => 'All roles', 'col' => 2, 'options' => FilterOptions::userRoles()],
            ]),
        ]);
    }

    public function update(AssignUserRoleRequest $request, int $user): RedirectResponse
    {
        Gate::authorize('assign-user-roles');

        $userModel = $this->userService->findOrFail($user, ['userRoles']);
        $roles = array_map('intval', $request->validated('roles'));

        if ($userModel->id === auth()->id() && $userModel->hasRole(UserRole::ADMIN) && ! in_array(UserRole::ADMIN, $roles, true)) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $this->userService->update($user, ['roles' => $roles]);

        return back()->with('success', 'Roles updated successfully.');
    }
}
