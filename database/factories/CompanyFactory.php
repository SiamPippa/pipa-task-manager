<?php

namespace Database\Factories;

use App\Models\Company;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    private static int $sequence = 0;

    public function definition(): array
    {
        $index = self::$sequence++;
        $name = RealisticData::companyName($this->faker, $index);

        return [
            'name' => $name,
            'code' => RealisticData::companyCode($name, $index),
            'email' => RealisticData::companyEmail($name),
            'phone' => RealisticData::companyPhone($this->faker),
            'logo' => null,
            'status' => $this->faker->boolean(92),
        ];
    }
}
