<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentNameUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_duplicate_department_name_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => 'HR',
            'code' => 'HR002',
        ]));

        $response->assertSessionHasErrors([
            'name' => 'Department name already exists for the selected company.',
        ]);
        $this->assertDatabaseCount('departments', 1);
    }

    public function test_store_allows_same_department_name_for_different_companies(): void
    {
        $companyA = Company::factory()->active()->create();
        $companyB = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $companyA->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($companyB);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($companyB, [
            'name' => 'HR',
            'code' => 'HR002',
        ]));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', [
            'company_id' => $companyB->id,
            'name' => 'HR',
            'code' => 'HR002',
        ]);
    }

    public function test_store_rejects_case_insensitive_duplicate_department_name(): void
    {
        $company = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => 'hr',
            'code' => 'HR002',
        ]));

        $response->assertSessionHasErrors([
            'name' => 'Department name already exists for the selected company.',
        ]);
    }

    public function test_store_trims_name_before_uniqueness_validation(): void
    {
        $company = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => '  HR  ',
            'code' => 'HR002',
        ]));

        $response->assertSessionHasErrors([
            'name' => 'Department name already exists for the selected company.',
        ]);
    }

    public function test_update_rejects_duplicate_department_name_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        $existing = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $department = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Finance',
            'code' => 'FIN001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('departments.update', $department), $this->validPayload($company, [
            'name' => 'HR',
            'code' => $department->code,
        ]));

        $response->assertSessionHasErrors([
            'name' => 'Department name already exists for the selected company.',
        ]);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Finance',
        ]);
        $this->assertDatabaseHas('departments', [
            'id' => $existing->id,
            'name' => 'HR',
        ]);
    }

    public function test_update_allows_keeping_same_department_name(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('departments.update', $department), $this->validPayload($company, [
            'name' => 'HR',
            'code' => 'HR001',
        ]));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
    }

    public function test_store_rejects_duplicate_department_code_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => 'Finance',
            'code' => 'HR001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Department code already exists for the selected company.',
        ]);
        $this->assertDatabaseCount('departments', 1);
    }

    public function test_store_allows_same_department_code_for_different_companies(): void
    {
        $companyA = Company::factory()->active()->create();
        $companyB = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $companyA->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($companyB);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($companyB, [
            'name' => 'HR',
            'code' => 'HR001',
        ]));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', [
            'company_id' => $companyB->id,
            'code' => 'HR001',
        ]);
    }

    public function test_store_rejects_case_insensitive_duplicate_department_code(): void
    {
        $company = Company::factory()->active()->create();
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => 'Finance',
            'code' => 'hr001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Department code already exists for the selected company.',
        ]);
    }

    public function test_store_normalizes_department_code_before_saving(): void
    {
        $company = Company::factory()->active()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($company, [
            'name' => 'Finance',
            'code' => '  fin001  ',
        ]));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', [
            'company_id' => $company->id,
            'name' => 'Finance',
            'code' => 'FIN001',
        ]);
    }

    public function test_update_rejects_duplicate_department_code_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        $existing = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $department = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Finance',
            'code' => 'FIN001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('departments.update', $department), $this->validPayload($company, [
            'name' => $department->name,
            'code' => 'HR001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Department code already exists for the selected company.',
        ]);
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'code' => 'FIN001',
        ]);
        $this->assertDatabaseHas('departments', [
            'id' => $existing->id,
            'code' => 'HR001',
        ]);
    }

    public function test_update_allows_keeping_same_department_code(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'HR',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('departments.update', $department), $this->validPayload($company, [
            'name' => 'Human Resources',
            'code' => 'hr001',
        ]));

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', [
            'id' => $department->id,
            'name' => 'Human Resources',
            'code' => 'HR001',
        ]);
    }

    private function validPayload(Company $company, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'name' => 'Engineering',
            'code' => 'ENG001',
            'status' => '1',
        ], $overrides);
    }

    private function adminUser(Company $company): User
    {
        $user = User::factory()->forOrganization($company)->create();
        $user->syncRoles([UserRole::ADMIN]);

        return $user;
    }
}
