<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;

class ProjectService extends BaseService implements ProjectServiceInterface
{
    public function __construct(ProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
