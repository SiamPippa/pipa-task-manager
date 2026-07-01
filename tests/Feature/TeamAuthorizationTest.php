<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_admin_can_create_edit_and_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $companyAdmin = $this->userWithRole($company, UserRole::COMPANY_ADMIN);
        $teamLead = $this->userWithRole($company, UserRole::TEAM_LEAD);
        $developer = $this->userWithRole($company, UserRole::DEVELOPER);

        $this->actingAs($companyAdmin)
            ->get(route('teams.create'))
            ->assertOk();

        $this->actingAs($companyAdmin)
            ->post(route('teams.store'), $this->teamPayload($company, $teamLead, $developer, [
                'name' => 'Delivery Squad',
                'code' => 'DLV001',
            ]))
            ->assertRedirect(route('teams.index'));

        $team = Team::query()->where('name', 'Delivery Squad')->firstOrFail();

        $this->assertDatabaseHas('team_members', [
            'team_id' => $team->id,
            'user_id' => $teamLead->id,
            'is_team_lead' => true,
        ]);

        $this->actingAs($companyAdmin)
            ->get(route('teams.edit', $team))
            ->assertOk()
            ->assertSee('value="'.$teamLead->id.'" selected', false)
            ->assertSee('value="'.$developer->id.'" selected', false);

        $this->actingAs($companyAdmin)
            ->put(route('teams.update', $team), $this->teamPayload($company, $teamLead, $developer, [
                'name' => 'Delivery Squad Updated',
                'code' => 'DLV002',
            ]))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Delivery Squad Updated',
            'code' => 'DLV002',
        ]);

        $this->actingAs($companyAdmin)
            ->delete(route('teams.destroy', $team))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_super_admin_can_create_edit_and_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $admin = $this->userWithRole($company, UserRole::SUPER_ADMIN);
        $teamLead = $this->userWithRole($company, UserRole::TEAM_LEAD);
        $developer = $this->userWithRole($company, UserRole::DEVELOPER);

        $this->actingAs($admin)
            ->get(route('teams.create'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('teams.store'), $this->teamPayload($company, $teamLead, $developer, [
                'name' => 'Platform Team',
                'code' => 'PLT001',
            ]))
            ->assertRedirect(route('teams.index'));

        $team = Team::query()->where('name', 'Platform Team')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('teams.update', $team), $this->teamPayload($company, $teamLead, $developer, [
                'name' => 'Platform Team Updated',
                'code' => 'PLT002',
            ]))
            ->assertRedirect(route('teams.index'));

        $this->actingAs($admin)
            ->delete(route('teams.destroy', $team))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_team_lead_cannot_create_edit_or_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $teamLead = $this->userWithRole($company, UserRole::TEAM_LEAD);
        $developer = $this->userWithRole($company, UserRole::DEVELOPER);
        $team = Team::factory()->create([
            'company_id' => $company->id,
            'team_lead_id' => $teamLead->id,
            'name' => 'Core Team',
            'code' => 'COR001',
        ]);

        $this->actingAs($teamLead)
            ->get(route('teams.create'))
            ->assertForbidden();

        $this->actingAs($teamLead)
            ->post(route('teams.store'), $this->teamPayload($company, $teamLead, $developer))
            ->assertForbidden();

        $this->actingAs($teamLead)
            ->get(route('teams.edit', $team))
            ->assertForbidden();

        $this->actingAs($teamLead)
            ->put(route('teams.update', $team), $this->teamPayload($company, $teamLead, $developer, [
                'name' => 'Blocked Edit',
            ]))
            ->assertForbidden();

        $this->actingAs($teamLead)
            ->delete(route('teams.destroy', $team))
            ->assertForbidden();
    }

    public function test_unauthorized_users_cannot_create_edit_or_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $generalUser = $this->userWithRole($company, UserRole::DEVELOPER);
        $manager = $this->userWithRole($company, UserRole::PROJECT_MANAGER);
        $teamLead = $this->userWithRole($company, UserRole::TEAM_LEAD);
        $developer = $this->userWithRole($company, UserRole::DEVELOPER);
        $team = Team::factory()->create([
            'company_id' => $company->id,
            'team_lead_id' => $teamLead->id,
            'name' => 'Core Team',
            'code' => 'COR001',
        ]);

        foreach ([$generalUser, $manager] as $user) {
            $this->actingAs($user)
                ->get(route('teams.create'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('teams.store'), $this->teamPayload($company, $teamLead, $developer, [
                    'name' => 'Blocked Team',
                    'code' => 'BLK001',
                ]))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('teams.edit', $team))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('teams.update', $team), $this->teamPayload($company, $teamLead, $developer, [
                    'name' => 'Blocked Edit',
                    'code' => 'BLK002',
                ]))
                ->assertForbidden();

            $this->actingAs($user)
                ->delete(route('teams.destroy', $team))
                ->assertForbidden();
        }

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Core Team',
            'code' => 'COR001',
        ]);
    }

    private function teamPayload(Company $company, User $teamLead, User $developer, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'name' => 'Team Alpha',
            'code' => 'ALP001',
            'status' => '1',
            'members' => [
                [
                    'user_id' => $teamLead->id,
                    'is_team_lead' => true,
                    'status' => true,
                ],
                [
                    'user_id' => $developer->id,
                    'is_team_lead' => false,
                    'status' => true,
                ],
            ],
        ], $overrides);
    }

    private function userWithRole(Company $company, string|int $role): User
    {
        $user = User::factory()->forOrganization($company)->create(['status' => true]);
        $user->syncRoles([$role]);

        return $user;
    }
}
