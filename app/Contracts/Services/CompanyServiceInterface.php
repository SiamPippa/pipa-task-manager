<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface CompanyServiceInterface extends BaseServiceInterface
{
    public function allActive(array $with = []): Collection;
}
