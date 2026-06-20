<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Support\Rbac;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(): View
    {
        Gate::authorize('view-rbac');

        return view('permissions.index', [
            'matrix' => Rbac::matrix(),
            'groups' => Permission::groups(),
            'labels' => Permission::labels(),
        ]);
    }

    public function show(int $role): View
    {
        Gate::authorize('view-rbac');

        abort_unless(isset(UserRole::labels()[$role]), 404);

        $rolePermissions = config('rbac.roles.'.$role, []);

        return view('permissions.show', [
            'roleId' => $role,
            'roleLabel' => UserRole::label($role),
            'rolePermissions' => $rolePermissions,
            'groups' => Permission::groups(),
            'labels' => Permission::labels(),
        ]);
    }
}
