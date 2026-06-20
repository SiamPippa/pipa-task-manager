<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
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

        return [
            'company_id' => Company::factory(),
            'department_id' => Department::factory(),
            'name' => $fullName,
            'code' => $code,
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'inactive', 'completed']),
        ];
    }
}
