<?php

namespace Database\Seeders;

use App\Enums\TaskType;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use App\Support\BranchNameGenerator;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public const MIN_TASKS_PER_PROJECT = 6;

    public const MAX_TASKS_PER_PROJECT = 10;

    public function run(): void
    {
        $faker = fake();
        $projects = Project::query()->orderBy('id')->get(['id', 'code', 'status', 'start_date', 'end_date']);
        $taskCounter = 1001;

        if ($projects->isEmpty()) {
            return;
        }

        foreach ($projects as $projectIndex => $project) {
            $module = ProjectModule::query()->firstOrCreate(
                ['project_id' => $project->id, 'name' => 'General'],
                [
                    'details' => 'Default module for project tasks.',
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'estimated_hours' => 0,
                ],
            );

            $taskCount = $faker->numberBetween(self::MIN_TASKS_PER_PROJECT, self::MAX_TASKS_PER_PROJECT);

            for ($i = 0; $i < $taskCount; $i++) {
                $titleIndex = ($projectIndex * self::MAX_TASKS_PER_PROJECT) + $i;
                $title = RealisticData::TASK_TITLES[$titleIndex % count(RealisticData::TASK_TITLES)];
                $status = RealisticData::taskStatusForIndex($projectIndex, $i, $taskCount, $project->status);
                $branchName = BranchNameGenerator::fromTitle($title);
                $suffix = 0;

                while (
                    Task::query()
                        ->where('project_id', $project->id)
                        ->where('branch_name', $branchName)
                        ->exists()
                ) {
                    $suffix++;
                    $branchName = BranchNameGenerator::fromTitle($title).'-'.$suffix;
                }

                Task::query()->create([
                    'project_id' => $project->id,
                    'project_module_id' => $module->id,
                    'jira_task_no' => RealisticData::jiraTaskNo($project->code, $taskCounter),
                    'title' => $title,
                    'branch_name' => $branchName,
                    'type' => $faker->randomElement(TaskType::values()),
                    'description' => '<p>'.$faker->paragraph(2).'</p>',
                    'estimate_hours' => $faker->randomFloat(2, 4, 32),
                    'status' => $status,
                ]);

                $taskCounter++;
            }
        }
    }
}
