<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Support\ActiveRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $authenticated = $this->authService->attemptLogin(
            $request->validated('email'),
            $request->validated('password'),
            $request->boolean('remember')
        );

        if (! $authenticated) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        $request->user()->load('roles');
        ActiveRole::resolve($request->user());

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function dashboard(): View
    {
        return view('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        ActiveRole::clear();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
