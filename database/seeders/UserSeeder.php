<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrCreate(
            ['code' => 'PIPPA'],
            [
                'name' => 'Pippa',
                'email' => 'hello@pippa.com',
                'phone' => null,
                'logo' => null,
                'status' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@pippa.com'],
            [
                'company_id' => $company->id,
                'department_id' => null,
                'designation_id' => null,
                'reporting_manager_id' => null,
                'name' => 'Siam M Zaque',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        )->syncRoles([UserRole::ADMIN]);
    }
}
