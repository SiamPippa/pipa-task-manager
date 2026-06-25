<?php

namespace App\Http\Middleware;

use App\Support\ActiveRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $user->loadMissing('userRoles');
            ActiveRole::resolve($user);
        }

        return $next($request);
    }
}
