<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    public function allActive(array $with = []): Collection;
}
