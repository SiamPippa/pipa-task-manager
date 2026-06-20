<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseServiceInterface
{
    public function paginate(array $filters = [], int $perPage = 15, array $with = []): Paginator;

    public function all(array $with = []): Collection;

    public function find(int $id, array $with = []): ?Model;

    public function findOrFail(int $id, array $with = []): Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;
}
