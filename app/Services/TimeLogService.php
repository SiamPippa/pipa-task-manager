<?php

namespace App\Services;

use App\Contracts\Repositories\TimeLogRepositoryInterface;
use App\Contracts\Services\TimeLogServiceInterface;

class TimeLogService extends BaseService implements TimeLogServiceInterface
{
    public function __construct(TimeLogRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
