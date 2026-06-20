<?php

namespace App\Services;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Contracts\Services\CompanyServiceInterface;

class CompanyService extends BaseService implements CompanyServiceInterface
{
    public function __construct(CompanyRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
