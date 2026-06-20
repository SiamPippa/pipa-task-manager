<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskAssignment>
 */
class TaskAssignmentFactory extends Factory
{
    protected $model = TaskAssignment::class;

    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'assigned_by' => null,
            'assigned_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
