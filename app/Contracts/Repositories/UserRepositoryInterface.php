<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?\App\Models\User;
}
