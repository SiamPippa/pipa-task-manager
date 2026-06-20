<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectTeamAssignmentRepositoryInterface;
use App\Contracts\Services\ProjectTeamAssignmentServiceInterface;

class ProjectTeamAssignmentService extends BaseService implements ProjectTeamAssignmentServiceInterface
{
    public function __construct(ProjectTeamAssignmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
