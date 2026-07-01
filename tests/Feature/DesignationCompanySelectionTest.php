<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignationCompanySelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_excludes_inactive_companies(): void
    {
        $activeCompany = Company::factory()->active()->create(['name' => 'Active Designation Co']);
        Company::factory()->inactive()->create(['name' => 'Inactive Designation Co']);
        $admin = $this->adminUser($activeCompany);

        $response = $this->actingAs($admin)->get(route('designations.create'));

        $response->assertOk();
        $response->assertSee('Active Designation Co', false);
        $response->assertDontSee('Inactive Designation Co', false);
    }

    public function test_store_rejects_inactive_company(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($inactiveCompany));

        $response->assertSessionHasErrors([
            'company_id' => 'Designation cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('designations', [
            'company_id' => $inactiveCompany->id,
            'code' => 'MGR001',
        ]);
    }

    public function test_store_rejects_manually_submitted_inactive_company_id(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($inactiveCompany, [
            'title' => 'Senior Analyst',
            'code' => 'SAN999',
        ]));

        $response->assertSessionHasErrors([
            'company_id' => 'Designation cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('designations', ['code' => 'SAN999']);
    }

    public function test_update_rejects_inactive_company(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $designation = Designation::factory()->create(['company_id' => $adminCompany->id]);
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->put(route('designations.update', $designation), $this->validPayload($inactiveCompany, [
            'title' => $designation->title,
            'code' => $designation->code,
        ]));

        $response->assertSessionHasErrors([
            'company_id' => 'Designation cannot be assigned to an inactive company.',
        ]);
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'company_id' => $adminCompany->id,
        ]);
    }

    public function test_locked_user_cannot_create_designation_under_inactive_company(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create();
        $user = User::factory()->forOrganization($inactiveCompany)->create();
        $user->syncRoles([UserRole::COMPANY_ADMIN]);

        $response = $this->actingAs($user)->post(route('designations.store'), [
            'title' => 'Operations Lead',
            'code' => 'OPS001',
            'status' => '1',
        ]);

        $response->assertSessionHasErrors([
            'company_id' => 'Designation cannot be created under an inactive company.',
        ]);
        $this->assertDatabaseMissing('designations', [
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

    public function test_index_still_lists_designations_for_inactive_companies(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create(['name' => 'Legacy Inactive Co']);
        Designation::factory()->create([
            'company_id' => $inactiveCompany->id,
            'title' => 'Legacy Designation',
        ]);
        $admin = $this->adminUser(Company::factory()->active()->create());

        $response = $this->actingAs($admin)->get(route('designations.index'));

        $response->assertOk();
        $response->assertSee('Legacy Designation', false);
    }

    private function validPayload(Company $company, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
            'status' => '1',
        ], $overrides);
    }

    private function adminUser(Company $company): User
    {
        $user = User::factory()->forOrganization($company)->create();
        $user->syncRoles([UserRole::SUPER_ADMIN]);

        return $user;
    }
}
