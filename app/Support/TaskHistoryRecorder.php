<?php

namespace App\Support;

use App\Models\Task;
use App\Models\TaskHistory;

class TaskHistoryRecorder
{
    public static function record(
        Task|int $task,
        string $action,
        array $meta = [],
        ?string $fromStatus = null,
        ?string $toStatus = null,
    ): void {
        TaskHistory::query()->create([
            'task_id' => $task instanceof Task ? $task->id : $task,
            'actor_id' => auth()->id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'meta' => $meta ?: null,
        ]);
    }
}

