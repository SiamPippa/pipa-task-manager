<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function attemptLogin(string $email, string $password, bool $remember = false): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return false;
        }

        Auth::login($user, $remember);

        return true;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }
}
