<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public const MAX_PER_COMPANY = 5;

    public function run(): void
    {
        $faker = fake();
        $companies = Company::query()->orderBy('id')->get();

        foreach ($companies as $companyIndex => $company) {
            for ($i = 0; $i < self::MAX_PER_COMPANY; $i++) {
                $name = RealisticData::DEPARTMENTS[($companyIndex * self::MAX_PER_COMPANY + $i) % count(RealisticData::DEPARTMENTS)];
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4));

                Department::query()->create([
                    'company_id' => $company->id,
                    'name' => $name,
                    'code' => $baseCode.$company->id.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'status' => $faker->boolean(95),
                ]);
            }
        }
    }
}
