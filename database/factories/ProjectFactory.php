<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Support\ProjectEstimatedHoursCalculator;
use Carbon\Carbon;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        $index = self::$sequence++;
        $name = RealisticData::PROJECT_NAMES[$index % count(RealisticData::PROJECT_NAMES)];
        $suffix = $this->faker->randomElement(['Phase 2', '2025', 'Q3', 'Rollout', 'Pilot', '']);
        $fullName = trim($name.' '.$suffix);
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 6)).($index + 1);
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+4 months');

        return [
            'company_id' => Company::factory(),
            'client_name' => $this->faker->company(),
            'description' => $this->faker->optional()->sentence(),
            'start_date' => Carbon::parse($startDate)->toDateString(),
            'end_date' => Carbon::parse($endDate)->toDateString(),
            'estimated_hours' => 0,
            'name' => $fullName,
            'code' => $code,
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive', 'completed']),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Project $project) {
            if ($project->company_id && $project->start_date && $project->end_date) {
                $settings = CompanySetting::query()
                    ->where('company_id', $project->company_id)
                    ->first();

                $project->estimated_hours = ProjectEstimatedHoursCalculator::estimatedHours(
                    Carbon::parse($project->start_date),
                    Carbon::parse($project->end_date),
                    ProjectEstimatedHoursCalculator::resolveHoursPerDay($settings),
                );
            }
        });
    }
}
