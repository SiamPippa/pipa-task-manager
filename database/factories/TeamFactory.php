<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(RealisticData::TEAM_NAMES);
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4)).$this->faker->unique()->numberBetween(10, 999);

        return [
            'company_id' => Company::factory(),
            'team_lead_id' => User::factory(),
            'name' => $name,
            'code' => $code,
            'status' => $this->faker->boolean(95),
        ];
    }
}
