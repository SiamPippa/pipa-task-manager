<?php

namespace App\Services;

use App\Contracts\Repositories\BaseRepositoryInterface;
use App\Contracts\Services\BaseServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService implements BaseServiceInterface
{
    public function __construct(
        protected readonly BaseRepositoryInterface $repository
    ) {}

    public function paginate(array $filters = [], int $perPage = 15, array $with = []): Paginator
    {
        return $this->repository
            ->filterQuery($filters, $with)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function all(array $with = []): Collection
    {
        return $this->repository->all($with);
    }

    public function find(int $id, array $with = []): ?Model
    {
        return $this->repository->find($id, $with);
    }

    public function findOrFail(int $id, array $with = []): Model
    {
        return $this->repository->findOrFail($id, $with);
    }

    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
