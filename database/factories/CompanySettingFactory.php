<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanySetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanySetting>
 */
class CompanySettingFactory extends Factory
{
    protected $model = CompanySetting::class;

    public function definition(): array
    {
        $startHour = $this->faker->randomElement(['08:00:00', '08:30:00', '09:00:00']);
        $endHour = $this->faker->randomElement(['17:00:00', '17:30:00', '18:00:00']);

        return [
            'company_id' => Company::factory(),
            'office_start_time' => $startHour,
            'office_end_time' => $endHour,
            'working_hours_per_day' => $this->faker->randomElement([7, 8, 8, 9]),
            'allow_manual_time_log' => $this->faker->boolean(85),
            'require_daily_report' => $this->faker->boolean(90),
        ];
    }
}
