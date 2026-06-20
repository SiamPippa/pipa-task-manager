<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeLog>
 */
class TimeLogFactory extends Factory
{
    protected $model = TimeLog::class;

    public function definition(): array
    {
        $workday = Carbon::instance($this->faker->dateTimeBetween('-3 months', 'now'))
            ->setTime($this->faker->numberBetween(8, 10), $this->faker->randomElement([0, 15, 30]), 0);
        $durationMinutes = $this->faker->numberBetween(45, 240);
        $endTime = $workday->copy()->addMinutes($durationMinutes);

        return [
            'project_id' => Project::factory(),
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'start_time' => $workday,
            'end_time' => $endTime,
            'total_minutes' => $durationMinutes,
            'note' => RealisticData::timeLogNote($this->faker),
        ];
    }
}
