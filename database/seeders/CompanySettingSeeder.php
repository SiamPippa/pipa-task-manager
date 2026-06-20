<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        Company::query()->each(function (Company $company) use ($faker) {
            CompanySetting::query()->create([
                'company_id' => $company->id,
                'office_start_time' => $faker->randomElement(['08:00:00', '08:30:00', '09:00:00']),
                'office_end_time' => $faker->randomElement(['17:00:00', '17:30:00', '18:00:00']),
                'working_hours_per_day' => $faker->randomElement([7, 8, 8, 9]),
                'allow_manual_time_log' => $faker->boolean(85),
                'require_daily_report' => $faker->boolean(90),
            ]);
        });
    }
}
