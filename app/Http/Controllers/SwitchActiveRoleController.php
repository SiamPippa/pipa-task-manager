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
            'role' => ['required', 'string', Rule::in(UserRole::values())],
        ]);

        $user = $request->user();
        $user->loadMissing('roles');

        ActiveRole::set($user, $request->string('role')->toString());

        return back();
    }
}
