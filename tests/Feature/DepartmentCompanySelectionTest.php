<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentCompanySelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_excludes_inactive_companies(): void
    {
        $activeCompany = Company::factory()->active()->create(['name' => 'Active Dept Co']);
        Company::factory()->inactive()->create(['name' => 'Inactive Dept Co']);
        $admin = $this->adminUser($activeCompany);

        $response = $this->actingAs($admin)->get(route('departments.create'));

        $response->assertOk();
        $response->assertSee('Active Dept Co', false);
        $response->assertDontSee('Inactive Dept Co', false);
    }

    public function test_store_rejects_inactive_company(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($inactiveCompany));

        $response->assertSessionHasErrors([
            'company_id' => 'Department cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('departments', [
            'company_id' => $inactiveCompany->id,
            'code' => 'ENG001',
        ]);
    }

    public function test_store_rejects_manually_submitted_inactive_company_id(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->post(route('departments.store'), $this->validPayload($inactiveCompany, [
            'name' => 'Operations',
            'code' => 'OPS999',
        ]));

        $response->assertSessionHasErrors([
            'company_id' => 'Department cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('departments', ['code' => 'OPS999']);
    }

    public function test_locked_user_cannot_create_department_under_inactive_company(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create();
        $department = Department::factory()->create(['company_id' => $inactiveCompany->id]);
        $user = User::factory()->forOrganization($inactiveCompany, $department)->create();
        $user->syncRoles([UserRole::DEPARTMENT_HEAD]);

        $response = $this->actingAs($user)->post(route('departments.store'), [
            'name' => 'Operations',
            'code' => 'OPS001',
            'status' => '1',
        ]);

        $response->assertSessionHasErrors([
            'company_id' => 'Department cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('departments', [
            'company_id' => $inactiveCompany->id,
            'code' => 'OPS001',
        ]);
    }

    public function test_company_lookup_excludes_inactive_companies(): void
    {
        $activeCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($activeCompany);

        $response = $this->actingAs($admin)->getJson(route('lookup', ['type' => 'companies']));

        $response->assertOk();
        $ids = collect($response->json())->pluck('id');

        $this->assertTrue($ids->contains($activeCompany->id));
        $this->assertFalse($ids->contains($inactiveCompany->id));
    }

    public function test_index_still_lists_departments_for_inactive_companies(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create(['name' => 'Legacy Inactive Co']);
        Department::factory()->create([
            'company_id' => $inactiveCompany->id,
            'name' => 'Legacy Department',
        ]);
        $admin = $this->adminUser(Company::factory()->active()->create());

        $response = $this->actingAs($admin)->get(route('departments.index'));

        $response->assertOk();
        $response->assertSee('Legacy Department', false);
    }

    public function test_index_table_shows_company_name_before_department_name(): void
    {
        $company = Company::factory()->active()->create(['name' => 'Acme Corporation']);
        Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Human Resources',
            'code' => 'HR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->get(route('departments.index'));

        $response->assertOk();
        $response->assertSeeInOrder(['Company Name', 'Department Name', 'Code', 'Status'], false);
        $response->assertSeeInOrder(['Acme Corporation', 'Human Resources'], false);
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
