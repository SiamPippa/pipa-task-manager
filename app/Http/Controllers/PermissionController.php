<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Support\Rbac;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

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

    public function show(string $role): View
    {
        Gate::authorize('view-rbac');

        abort_unless(isset(UserRole::labels()[$role]), 404);

        $rolePermissions = Role::query()
            ->with('permissions')
            ->where('name', $role)
            ->firstOrFail()
            ->permissions
            ->pluck('name')
            ->all();

        return view('permissions.show', [
            'roleId' => $role,
            'roleLabel' => UserRole::label($role),
            'rolePermissions' => $rolePermissions,
            'groups' => Permission::groups(),
            'labels' => Permission::labels(),
        ]);
    }
}
