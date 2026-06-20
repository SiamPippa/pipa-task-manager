<?php

namespace App\Services;

use App\Contracts\Repositories\CompanySettingRepositoryInterface;
use App\Contracts\Services\CompanySettingServiceInterface;

class CompanySettingService extends BaseService implements CompanySettingServiceInterface
{
    public function __construct(CompanySettingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
