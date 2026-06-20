<?php

namespace App\Services;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\TaskServiceInterface;

class TaskService extends BaseService implements TaskServiceInterface
{
    public function __construct(TaskRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
