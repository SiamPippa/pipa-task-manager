<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(array $with = []): Collection;

    public function filterQuery(array $filters = [], array $with = []): Builder;

    public function find(int $id, array $with = []): ?Model;

    public function findOrFail(int $id, array $with = []): Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;
}
