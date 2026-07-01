<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => 'EMP'.$this->faker->unique()->numberBetween(1000, 9999),
            'designation_id' => null,
            'reporting_manager_id' => null,
            'office_location_id' => null,
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => $this->faker->boolean(94),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->syncRoles([UserRole::DEVELOPER]);
        });
    }

    public function forOrganization(
        Company $company,
        mixed $department = null,
        ?Designation $designation = null
    ): static {
        return $this->state(fn () => [
            'company_id' => $company->id,
            'designation_id' => $designation?->id,
        ]);
    }
}
