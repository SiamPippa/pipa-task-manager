<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_rejects_empty_working_hours_per_day(): void
    {
        $company = Company::factory()->create();
        $setting = CompanySetting::factory()->create([
            'company_id' => $company->id,
            'working_hours_per_day' => 8,
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('company-settings.update', $setting), [
            'company_id' => $company->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'working_hours_per_day' => '',
            'allow_manual_time_log' => '1',
            'require_daily_report' => '1',
        ]);

        $response->assertSessionHasErrors([
            'working_hours_per_day' => 'The working hours per day field is required.',
        ]);
        $this->assertSame(8, $setting->fresh()->working_hours_per_day);
    }

    public function test_store_rejects_missing_working_hours_per_day(): void
    {
        $company = Company::factory()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('company-settings.store'), [
            'company_id' => $company->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'allow_manual_time_log' => '1',
            'require_daily_report' => '1',
        ]);

        $response->assertSessionHasErrors([
            'working_hours_per_day' => 'The working hours per day field is required.',
        ]);
        $this->assertDatabaseMissing('company_settings', [
            'company_id' => $company->id,
        ]);
    }

    public function test_update_accepts_valid_working_hours_per_day(): void
    {
        $company = Company::factory()->create();
        $setting = CompanySetting::factory()->create([
            'company_id' => $company->id,
            'working_hours_per_day' => 8,
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('company-settings.update', $setting), [
            'company_id' => $company->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
            'working_hours_per_day' => '7',
            'allow_manual_time_log' => '1',
            'require_daily_report' => '1',
        ]);

        $response->assertRedirect(route('company-settings.index'));
        $response->assertSessionHasNoErrors();
        $this->assertSame(7, $setting->fresh()->working_hours_per_day);
    }

    public function test_store_rejects_equal_office_times(): void
    {
        $company = Company::factory()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('company-settings.store'), $this->validPayload($company, [
            'office_start_time' => '09:00',
            'office_end_time' => '09:00',
        ]));

        $response->assertSessionHasErrors([
            'office_end_time' => 'Office end time must be greater than office start time.',
        ]);
        $this->assertDatabaseMissing('company_settings', ['company_id' => $company->id]);
    }

    public function test_store_rejects_office_end_before_start(): void
    {
        $company = Company::factory()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('company-settings.store'), $this->validPayload($company, [
            'office_start_time' => '18:00',
            'office_end_time' => '09:00',
        ]));

        $response->assertSessionHasErrors([
            'office_end_time' => 'Office end time must be greater than office start time.',
        ]);
        $this->assertDatabaseMissing('company_settings', ['company_id' => $company->id]);
    }

    public function test_update_rejects_equal_office_times(): void
    {
        $company = Company::factory()->create();
        $setting = CompanySetting::factory()->create([
            'company_id' => $company->id,
            'office_start_time' => '09:00:00',
            'office_end_time' => '18:00:00',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('company-settings.update', $setting), $this->validPayload($company, [
            'office_start_time' => '10:00',
            'office_end_time' => '10:00',
        ]));

        $response->assertSessionHasErrors([
            'office_end_time' => 'Office end time must be greater than office start time.',
        ]);
        $this->assertSame('18:00:00', $setting->fresh()->office_end_time);
    }

    public function test_update_rejects_office_end_before_start(): void
    {
        $company = Company::factory()->create();
        $setting = CompanySetting::factory()->create([
            'company_id' => $company->id,
            'office_start_time' => '09:00:00',
            'office_end_time' => '18:00:00',
        ]);
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->put(route('company-settings.update', $setting), $this->validPayload($company, [
            'office_start_time' => '17:00',
            'office_end_time' => '08:00',
        ]));

        $response->assertSessionHasErrors([
            'office_end_time' => 'Office end time must be greater than office start time.',
        ]);
        $this->assertSame('18:00:00', $setting->fresh()->office_end_time);
    }

    public function test_store_accepts_valid_office_time_range(): void
    {
        $company = Company::factory()->create();
        $admin = $this->adminUser($company);

        $response = $this->actingAs($admin)->post(route('company-settings.store'), $this->validPayload($company, [
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
        ]));

        $response->assertRedirect(route('company-settings.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('company_settings', [
            'company_id' => $company->id,
            'office_start_time' => '09:00',
            'office_end_time' => '18:00',
        ]);
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
        $user = User::factory()->forOrganization($company)->create(['status' => true]);
        $user->syncRoles([UserRole::SUPER_ADMIN]);

        return $user;
    }
}
