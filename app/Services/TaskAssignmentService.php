<?php

namespace App\Services;

use App\Contracts\Repositories\TaskAssignmentRepositoryInterface;
use App\Contracts\Services\TaskAssignmentServiceInterface;
use App\Support\TaskHistoryRecorder;
use Illuminate\Database\Eloquent\Model;

class TaskAssignmentService extends BaseService implements TaskAssignmentServiceInterface
{
    public function __construct(TaskAssignmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $assignment = parent::create($data);

        TaskHistoryRecorder::record($assignment->task_id, 'task_assignment_created', [
            'assignee_id' => $assignment->user_id,
            'assigned_by' => $assignment->assigned_by,
            'assigned_at' => optional($assignment->assigned_at)?->toDateTimeString(),
        ]);

        return $assignment;
    }

    public function update(int $id, array $data): Model
    {
        $before = $this->findOrFail($id);
        $assignment = parent::update($id, $data);

        TaskHistoryRecorder::record($assignment->task_id, 'task_assignment_updated', [
            'from_assignee_id' => $before->user_id,
            'to_assignee_id' => $assignment->user_id,
        ]);

        return $assignment;
    }

    public function delete(int $id): bool
    {
        $assignment = $this->findOrFail($id);
        $deleted = parent::delete($id);

        if ($deleted) {
            TaskHistoryRecorder::record($assignment->task_id, 'task_assignment_deleted', [
                'assignee_id' => $assignment->user_id,
            ]);
        }

        return $deleted;
    }
}
