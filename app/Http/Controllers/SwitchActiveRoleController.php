<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Support\ActiveRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SwitchActiveRoleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'integer', Rule::in(UserRole::values())],
        ]);

        $user = $request->user();
        $user->loadMissing('userRoles');

        ActiveRole::set($user, $request->integer('role'));

        return back();
    }
}
