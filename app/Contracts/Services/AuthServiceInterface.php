<?php

namespace App\Contracts\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function attemptLogin(string $email, string $password, bool $remember = false): bool;

    public function logout(): void;

    public function getAuthenticatedUser(): ?User;
}
