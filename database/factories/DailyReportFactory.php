<?php

namespace Database\Factories;

use App\Models\DailyReport;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyReport>
 */
class DailyReportFactory extends Factory
{
    protected $model = DailyReport::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'task_id' => Task::factory(),
            'report_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'summary' => $this->faker->randomElement(RealisticData::REPORT_SUMMARIES),
            'blocker' => $this->faker->randomElement(RealisticData::BLOCKERS),
            'tomorrow_plan' => $this->faker->randomElement(RealisticData::TOMORROW_PLANS),
            'progress_percent' => $this->faker->numberBetween(10, 100),
        ];
    }
}
