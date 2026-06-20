<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
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
            'jira_task_no' => 'TASK-'.$number,
            'title' => $title,
            'description' => $this->faker->paragraph(2),
            'estimate_hours' => $this->faker->randomFloat(2, 2, 40),
            'status' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
        ];
    }
}
