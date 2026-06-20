<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Department;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(RealisticData::DEPARTMENTS);
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 4)).$this->faker->unique()->numberBetween(10, 999);

        return [
            'company_id' => Company::factory(),
            'name' => $name,
            'code' => $code,
            'status' => $this->faker->boolean(95),
        ];
    }
}
