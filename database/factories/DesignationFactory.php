<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Designation;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Designation>
 */
class DesignationFactory extends Factory
{
    protected $model = Designation::class;

    public function definition(): array
    {
        $title = $this->faker->randomElement(RealisticData::DESIGNATIONS);
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 4)).$this->faker->unique()->numberBetween(10, 999);

        return [
            'company_id' => Company::factory(),
            'title' => $title,
            'code' => $code,
            'status' => $this->faker->boolean(95),
        ];
    }
}
