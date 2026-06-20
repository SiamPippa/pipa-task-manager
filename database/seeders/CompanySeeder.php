<?php

namespace Database\Seeders;

use App\Models\Company;
use Database\Seeders\Support\RealisticData;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public const MAX = 2;

    public function run(): void
    {
        $faker = fake();

        for ($i = 0; $i < self::MAX; $i++) {
            $name = RealisticData::companyName($faker, $i);

            Company::query()->create([
                'name' => $name,
                'code' => RealisticData::companyCode($name, $i),
                'email' => RealisticData::companyEmail($name),
                'phone' => RealisticData::companyPhone($faker),
                'logo' => null,
                'status' => true,
            ]);
        }
    }
}
