<?php

namespace App\Services;

use App\Contracts\Repositories\TaskRepositoryInterface;
use App\Contracts\Services\TaskServiceInterface;
use App\Models\ProjectModule;
use App\Support\TaskHistoryRecorder;
use App\Support\BranchNameGenerator;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Model;

class TaskService extends BaseService implements TaskServiceInterface
{
    public function __construct(TaskRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $task = $this->repository->create($this->prepareTaskData($data));

        TaskHistoryRecorder::record($task, 'task_created', [
            'title' => $task->title,
            'status' => $task->status,
        ], null, $task->status);

        return $task;
    }

    public function update(int $id, array $data): Model
    {
        $before = $this->findOrFail($id);
        $beforeStatus = $before->status;
        $beforeSnapshot = $before->only([
            'project_id',
            'project_module_id',
            'jira_task_no',
            'title',
            'branch_name',
            'type',
            'description',
            'estimate_hours',
            'status',
            'priority',
            'due_date',
            'qa_status',
            'qa_comment',
        ]);

        $task = $this->repository->update($id, $this->prepareTaskData($data));

        $afterSnapshot = $task->only(array_keys($beforeSnapshot));
        $changes = [];
        foreach ($beforeSnapshot as $field => $oldValue) {
            $newValue = $afterSnapshot[$field] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'from' => $oldValue,
                    'to' => $newValue,
                ];
            }
        }

        if ($changes !== []) {
            TaskHistoryRecorder::record($task, 'task_updated', ['changes' => $changes], $beforeStatus, $task->status);
        }

        if ($beforeStatus !== $task->status) {
            TaskHistoryRecorder::record($task, 'task_status_changed', [], $beforeStatus, $task->status);
        }

        return $task;
    }

    private function prepareTaskData(array $data): array
    {
        if (empty($data['branch_name']) && ! empty($data['title'])) {
            $data['branch_name'] = BranchNameGenerator::fromTitle($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $data['description'] = HtmlSanitizer::clean($data['description']);
        }

        if (! empty($data['project_module_id'])) {
            $module = ProjectModule::query()->find($data['project_module_id']);

            if ($module) {
                $data['project_id'] = $module->project_id;
            }
        }

        return $data;
    }
}
