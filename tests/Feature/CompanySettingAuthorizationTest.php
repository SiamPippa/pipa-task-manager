<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_head_can_view_index(): void
    {
        [$company, $setting, $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.index'));

        $response->assertOk();
        $response->assertSee($company->name, false);
    }

    public function test_department_head_can_view_own_company_setting(): void
    {
        [, $setting, $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.show', $setting));

        $response->assertOk();
    }

    public function test_department_head_cannot_access_create_form(): void
    {
        [, , $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.create'));

        $response->assertForbidden();
    }

    public function test_department_head_cannot_store_company_setting(): void
    {
        [$company, , $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->post(route('company-settings.store'), $this->validPayload($company));

        $response->assertForbidden();
    }

    public function test_department_head_cannot_access_edit_form(): void
    {
        [, $setting, $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.edit', $setting));

        $response->assertForbidden();
    }

    public function test_department_head_cannot_update_company_setting(): void
    {
        [$company, $setting, $departmentHead] = $this->departmentHeadContext();

        $originalHours = $setting->working_hours_per_day;

        $response = $this->actingAs($departmentHead)->put(route('company-settings.update', $setting), $this->validPayload($company, [
            'working_hours_per_day' => '7',
        ]));

        $response->assertForbidden();
        $this->assertSame($originalHours, $setting->fresh()->working_hours_per_day);
    }

    public function test_department_head_cannot_delete_company_setting(): void
    {
        [, $setting, $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->delete(route('company-settings.destroy', $setting));

        $response->assertForbidden();
        $this->assertDatabaseHas('company_settings', ['id' => $setting->id]);
    }

    public function test_department_head_index_hides_edit_and_delete_actions(): void
    {
        [, , $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.index'));

        $response->assertOk();
        $response->assertDontSee('btn-warning', false);
        $response->assertDontSee('>Delete<', false);
    }

    public function test_department_head_show_hides_edit_button(): void
    {
        [, $setting, $departmentHead] = $this->departmentHeadContext();

        $response = $this->actingAs($departmentHead)->get(route('company-settings.show', $setting));

        $response->assertOk();
        $response->assertDontSee('btn-warning', false);
    }

    public function test_admin_can_update_company_setting(): void
    {
        [$company, $setting] = $this->departmentHeadContext();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('company-settings.update', $setting), $this->validPayload($company, [
            'working_hours_per_day' => '7',
        ]));

        $response->assertRedirect(route('company-settings.index'));
        $this->assertSame(7, $setting->fresh()->working_hours_per_day);
    }

    /**
     * @return array{0: Company, 1: CompanySetting, 2: User}
     */
    private function departmentHeadContext(): array
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $setting = CompanySetting::factory()->create(['company_id' => $company->id]);
        $departmentHead = User::factory()->forOrganization($company, $department)->create();
        $departmentHead->syncRoles([UserRole::DEPARTMENT_HEAD]);

        return [$company, $setting, $departmentHead];
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(Company $company, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'working_hours_per_day' => '8',
            'allow_manual_time_log' => '1',
            'require_daily_report' => '1',
        ], $overrides);
    }

    private function adminUser(Company $company): User
    {
        $user = User::factory()->forOrganization($company)->create();
        $user->syncRoles([UserRole::ADMIN]);

        return $user;
    }
}
