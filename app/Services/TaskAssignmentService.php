<?php

namespace App\Services;

use App\Contracts\Repositories\TaskAssignmentRepositoryInterface;
use App\Contracts\Services\TaskAssignmentServiceInterface;

class TaskAssignmentService extends BaseService implements TaskAssignmentServiceInterface
{
    public function __construct(TaskAssignmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
