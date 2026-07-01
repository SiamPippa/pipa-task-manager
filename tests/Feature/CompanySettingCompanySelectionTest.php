<?php

namespace Tests\Feature;

use App\Contracts\Repositories\CompanyRepositoryInterface;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingCompanySelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_excludes_inactive_companies(): void
    {
        $activeCompany = Company::factory()->active()->create(['name' => 'Active Settings Co']);
        Company::factory()->inactive()->create(['name' => 'Inactive Settings Co']);
        $admin = $this->adminUser($activeCompany);

        $response = $this->actingAs($admin)->get(route('company-settings.create'));

        $response->assertOk();
        $response->assertSee('Active Settings Co', false);
        $response->assertDontSee('Inactive Settings Co', false);
    }

    public function test_store_rejects_inactive_company(): void
    {
        $adminCompany = Company::factory()->active()->create();
        $inactiveCompany = Company::factory()->inactive()->create();
        $admin = $this->adminUser($adminCompany);

        $response = $this->actingAs($admin)->post(route('company-settings.store'), [
            'company_id' => $inactiveCompany->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'working_hours_per_day' => '8',
            'allow_manual_time_log' => '1',
            'require_daily_report' => '1',
        ]);

        $response->assertSessionHasErrors([
            'company_id' => 'Company Settings cannot be created for an inactive company.',
        ]);
        $this->assertDatabaseMissing('company_settings', [
            'company_id' => $inactiveCompany->id,
        ]);
    }

    public function test_index_still_lists_settings_for_inactive_companies(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create(['name' => 'Legacy Inactive Co']);
        CompanySetting::factory()->create(['company_id' => $inactiveCompany->id]);
        $admin = $this->adminUser(Company::factory()->active()->create());

        $response = $this->actingAs($admin)->get(route('company-settings.index'));

        $response->assertOk();
        $response->assertSee('Legacy Inactive Co', false);
    }

    public function test_show_displays_setting_for_inactive_company(): void
    {
        $inactiveCompany = Company::factory()->inactive()->create(['name' => 'Archived Inactive Co']);
        $setting = CompanySetting::factory()->create(['company_id' => $inactiveCompany->id]);
        $admin = $this->adminUser(Company::factory()->active()->create());

        $response = $this->actingAs($admin)->get(route('company-settings.show', $setting));

        $response->assertOk();
        $response->assertSee('Archived Inactive Co', false);
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

    public function test_company_repository_all_active_excludes_inactive_companies(): void
    {
        $activeCompany = Company::factory()->active()->create();
        Company::factory()->inactive()->create();

        $companies = app(CompanyRepositoryInterface::class)->allActive();

        $this->assertTrue($companies->contains('id', $activeCompany->id));
        $this->assertSame(1, $companies->count());
    }

    private function adminUser(Company $company): User
    {
        $user = User::factory()->forOrganization($company)->create();
        $user->syncRoles([UserRole::SUPER_ADMIN]);

        return $user;
    }
}
