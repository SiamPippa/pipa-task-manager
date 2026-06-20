<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Project;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public const PROJECTS_PER_DEPARTMENT = 3;

    public function run(): void
    {
        $faker = fake();
        $departments = Department::query()->orderBy('id')->get();

        if ($departments->isEmpty()) {
            return;
        }

        $projectIndex = 0;

        foreach ($departments as $department) {
            for ($i = 0; $i < self::PROJECTS_PER_DEPARTMENT; $i++) {
                $baseName = RealisticData::PROJECT_NAMES[$projectIndex % count(RealisticData::PROJECT_NAMES)];
                $suffix = $faker->randomElement(['Phase 2', '2025', 'Q3 Rollout', 'Pilot', '']);
                $name = trim($baseName.' '.$suffix);
                $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $baseName), 0, 6)).$department->id.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

                Project::query()->create([
                    'company_id' => $department->company_id,
                    'department_id' => $department->id,
                    'name' => $name,
                    'code' => $code,
                    'status' => $faker->randomElement(['active', 'active', 'active', 'inactive', 'completed']),
                ]);

                $projectIndex++;
            }
        }
    }
}
