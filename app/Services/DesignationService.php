<?php

namespace App\Services;

use App\Contracts\Repositories\DesignationRepositoryInterface;
use App\Contracts\Services\DesignationServiceInterface;

class DesignationService extends BaseService implements DesignationServiceInterface
{
    public function __construct(DesignationRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
