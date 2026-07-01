<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected function model(): string
    {
        return User::class;
    }

    protected function applyFilters($query, array $filters): void
    {
        $this->applyExactFilter($query, 'company_id', $filters['company_id'] ?? null);
        $this->applyExactFilter($query, 'designation_id', $filters['designation_id'] ?? null);

        if (filled($filters['role'] ?? null)) {
            $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', $filters['role']));
        }
        $this->applySearchFilter($query, $filters['search'] ?? null, ['name', 'email']);
        $this->applyBooleanFilter($query, 'status', $filters['status'] ?? null);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }
}
