<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Designation;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public const MAX_PER_COMPANY = 8;

    public function run(): void
    {
        $faker = fake();
        $companies = Company::query()->orderBy('id')->get();
        $designationPool = RealisticData::DESIGNATIONS;
        $poolCount = count($designationPool);

        foreach ($companies as $companyIndex => $company) {
            for ($i = 0; $i < self::MAX_PER_COMPANY; $i++) {
                $title = $designationPool[($companyIndex * self::MAX_PER_COMPANY + $i) % $poolCount];
                $baseCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 4));
                $code = $baseCode.$company->id.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);

                Designation::query()->updateOrCreate([
                    'company_id' => $company->id,
                    'title' => $title,
                ], [
                    'code' => $code,
                    'status' => $faker->boolean(95),
                ]);
            }
        }
    }
}
