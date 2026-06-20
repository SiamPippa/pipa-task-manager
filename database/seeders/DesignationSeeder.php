<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public const MAX_PER_DEPARTMENT = 5;

    public function run(): void
    {
        $faker = fake();
        $departments = Department::query()->orderBy('id')->get();

        foreach ($departments as $departmentIndex => $department) {
            for ($i = 0; $i < self::MAX_PER_DEPARTMENT; $i++) {
                $title = RealisticData::DESIGNATIONS[($departmentIndex * self::MAX_PER_DEPARTMENT + $i) % count(RealisticData::DESIGNATIONS)];
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 4));

                Designation::query()->create([
                    'company_id' => $department->company_id,
                    'title' => $title,
                    'code' => $baseCode.$department->id.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'status' => $faker->boolean(95),
                ]);
            }
        }
    }
}
