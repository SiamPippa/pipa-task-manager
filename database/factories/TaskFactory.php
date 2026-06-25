<?php

namespace Database\Factories;

use App\Enums\TaskType;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use App\Support\BranchNameGenerator;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        $number = 1000 + self::$sequence++;
        $title = $this->faker->randomElement(RealisticData::TASK_TITLES);

        return [
            'project_id' => Project::factory(),
            'jira_task_no' => $this->faker->boolean(70) ? 'TASK-'.$number : null,
            'title' => $title,
            'branch_name' => BranchNameGenerator::fromTitle($title),
            'type' => $this->faker->randomElement(TaskType::values()),
            'description' => '<p>'.$this->faker->paragraph(2).'</p>',
            'estimate_hours' => $this->faker->randomFloat(2, 2, 40),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Task $task) {
            if ($task->project_module_id) {
                return;
            }

            $project = $task->project_id
                ? Project::query()->find($task->project_id)
                : null;

            if (! $project) {
                $project = Project::factory()->create();
                $task->project_id = $project->id;
            }

            $module = ProjectModule::query()->firstOrCreate(
                ['project_id' => $project->id, 'name' => 'General'],
                [
                    'details' => null,
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'estimated_hours' => 0,
                ],
            );

            $task->project_module_id = $module->id;
        });
    }
}
