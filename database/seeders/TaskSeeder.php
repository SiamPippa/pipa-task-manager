<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public const MIN_TASKS_PER_PROJECT = 6;

    public const MAX_TASKS_PER_PROJECT = 10;

    public function run(): void
    {
        $faker = fake();
        $projects = Project::query()->orderBy('id')->get(['id', 'code', 'status']);
        $taskCounter = 1001;

        if ($projects->isEmpty()) {
            return;
        }

        foreach ($projects as $projectIndex => $project) {
            $taskCount = $faker->numberBetween(self::MIN_TASKS_PER_PROJECT, self::MAX_TASKS_PER_PROJECT);

            for ($i = 0; $i < $taskCount; $i++) {
                $titleIndex = ($projectIndex * self::MAX_TASKS_PER_PROJECT) + $i;
                $status = RealisticData::taskStatusForIndex($projectIndex, $i, $taskCount, $project->status);

                Task::query()->create([
                    'project_id' => $project->id,
                    'jira_task_no' => RealisticData::jiraTaskNo($project->code, $taskCounter),
                    'title' => RealisticData::TASK_TITLES[$titleIndex % count(RealisticData::TASK_TITLES)],
                    'description' => $faker->paragraph(2),
                    'estimate_hours' => $faker->randomFloat(2, 4, 32),
                    'status' => $status,
                ]);

                $taskCounter++;
            }
        }
    }
}
