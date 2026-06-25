<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignationUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_duplicate_designation_title_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($company, [
            'title' => 'Manager',
            'code' => 'MGR002',
        ]));

        $response->assertSessionHasErrors([
            'title' => 'Designation title already exists for the selected company.',
        ]);
        $this->assertDatabaseCount('designations', 1);
    }

    public function test_store_rejects_duplicate_designation_code_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($company, [
            'title' => 'Senior Manager',
            'code' => 'MGR001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Designation code already exists for the selected company.',
        ]);
        $this->assertDatabaseCount('designations', 1);
    }

    public function test_store_allows_same_title_and_code_for_different_companies(): void
    {
        $companyA = Company::factory()->active()->create();
        $companyB = Company::factory()->active()->create();
        Designation::factory()->create([
            'company_id' => $companyA->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($companyB);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($companyB, [
            'title' => 'Manager',
            'code' => 'MGR001',
        ]));

        $response->assertRedirect(route('designations.index'));
        $this->assertDatabaseHas('designations', [
            'company_id' => $companyB->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
    }

    public function test_store_rejects_case_insensitive_duplicate_title(): void
    {
        $company = Company::factory()->active()->create();
        Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($company, [
            'title' => 'manager',
            'code' => 'MGR002',
        ]));

        $response->assertSessionHasErrors([
            'title' => 'Designation title already exists for the selected company.',
        ]);
    }

    public function test_store_rejects_case_insensitive_duplicate_code(): void
    {
        $company = Company::factory()->active()->create();
        Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($company, [
            'title' => 'Analyst',
            'code' => 'mgr001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Designation code already exists for the selected company.',
        ]);
    }

    public function test_store_trims_title_and_normalizes_code_before_saving(): void
    {
        $company = Company::factory()->active()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('designations.store'), $this->validPayload($company, [
            'title' => '  Senior Analyst  ',
            'code' => '  san001  ',
        ]));

        $response->assertRedirect(route('designations.index'));
        $this->assertDatabaseHas('designations', [
            'company_id' => $company->id,
            'title' => 'Senior Analyst',
            'code' => 'SAN001',
        ]);
    }

    public function test_store_rejects_repeated_identical_submission(): void
    {
        $company = Company::factory()->active()->create();
        $admin = $this->adminUser($company);
        $payload = $this->validPayload($company, [
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);

        $this->actingAs($admin)->post(route('designations.store'), $payload)
            ->assertRedirect(route('designations.index'));

        $response = $this->actingAs($admin)->post(route('designations.store'), $payload);

        $response->assertSessionHasErrors([
            'title' => 'Designation title already exists for the selected company.',
            'code' => 'Designation code already exists for the selected company.',
        ]);
        $this->assertDatabaseCount('designations', 1);
    }

    public function test_update_rejects_duplicate_title_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        $existing = Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $designation = Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Analyst',
            'code' => 'ANL001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('designations.update', $designation), $this->validPayload($company, [
            'title' => 'Manager',
            'code' => $designation->code,
        ]));

        $response->assertSessionHasErrors([
            'title' => 'Designation title already exists for the selected company.',
        ]);
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'title' => 'Analyst',
        ]);
        $this->assertDatabaseHas('designations', [
            'id' => $existing->id,
            'title' => 'Manager',
        ]);
    }

    public function test_update_rejects_duplicate_code_for_same_company(): void
    {
        $company = Company::factory()->active()->create();
        $existing = Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $designation = Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Analyst',
            'code' => 'ANL001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('designations.update', $designation), $this->validPayload($company, [
            'title' => $designation->title,
            'code' => 'MGR001',
        ]));

        $response->assertSessionHasErrors([
            'code' => 'Designation code already exists for the selected company.',
        ]);
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'code' => 'ANL001',
        ]);
    }

    public function test_update_allows_keeping_same_title_and_code(): void
    {
        $company = Company::factory()->active()->create();
        $designation = Designation::factory()->create([
            'company_id' => $company->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('designations.update', $designation), $this->validPayload($company, [
            'title' => 'manager',
            'code' => 'mgr001',
        ]));

        $response->assertRedirect(route('designations.index'));
        $this->assertDatabaseHas('designations', [
            'id' => $designation->id,
            'title' => 'Manager',
            'code' => 'MGR001',
        ]);
    }

    private function validPayload(Company $company, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'title' => 'Engineer',
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
