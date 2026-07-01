<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Project;
use App\Support\ProjectEstimatedHoursCalculator;
use Carbon\Carbon;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public const PROJECTS_PER_COMPANY = 3;

    public function run(): void
    {
        $faker = fake();
        $companies = Company::query()->orderBy('id')->get();

        if ($companies->isEmpty()) {
            return;
        }

        $projectIndex = 0;

        foreach ($companies as $company) {
            for ($i = 0; $i < self::PROJECTS_PER_COMPANY; $i++) {
                $baseName = RealisticData::PROJECT_NAMES[$projectIndex % count(RealisticData::PROJECT_NAMES)];
                $suffix = $faker->randomElement(['Phase 2', '2025', 'Q3 Rollout', 'Pilot', '']);
                $name = trim($baseName.' '.$suffix);
                $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $baseName), 0, 6)).$company->id.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                $startDate = Carbon::parse($faker->dateTimeBetween('-2 months', 'now'));
                $endDate = Carbon::parse($faker->dateTimeBetween($startDate, '+5 months'));
                $settings = CompanySetting::query()
                    ->where('company_id', $company->id)
                    ->first();

                Project::query()->create([
                    'company_id' => $company->id,
                    'client_name' => $faker->company(),
                    'description' => $faker->optional()->sentence(),
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'estimated_hours' => ProjectEstimatedHoursCalculator::estimatedHours(
                        $startDate,
                        $endDate,
                        ProjectEstimatedHoursCalculator::resolveHoursPerDay($settings),
                    ),
                    'name' => $name,
                    'code' => $code,
                    'status' => $faker->randomElement(['active', 'active', 'active', 'inactive', 'completed']),
                ]);

                $projectIndex++;
            }
        }
    }
}
