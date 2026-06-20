<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use App\Repositories\Concerns\AppliesListFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    use AppliesListFilters;

    abstract protected function model(): string;

    protected function applyFilters($query, array $filters): void
    {
        //
    }

    public function all(array $with = []): Collection
    {
        $query = $this->query();

        if ($with) {
            $query->with($with);
        }

        return $query->latest()->get();
    }

    public function filterQuery(array $filters = [], array $with = []): Builder
    {
        $query = $this->query();

        if ($with) {
            $query->with($with);
        }

        $this->applyFilters($query, $filters);

        return $query;
    }

    public function find(int $id, array $with = []): ?Model
    {
        $query = $this->query();

        if ($with) {
            $query->with($with);
        }

        return $query->find($id);
    }

    public function findOrFail(int $id, array $with = []): Model
    {
        $query = $this->query();

        if ($with) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model()::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);

        return $record->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->findOrFail($id)->delete();
    }

    protected function query()
    {
        return $this->model()::query();
    }
}
