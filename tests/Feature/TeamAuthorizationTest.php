<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_head_can_create_edit_and_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $departmentHead = $this->userWithRole($company, $department, UserRole::DEPARTMENT_HEAD);
        $teamLead = $this->userWithRole($company, $department, UserRole::TEAM_LEAD);

        $this->actingAs($departmentHead)
            ->get(route('teams.create'))
            ->assertOk();

        $this->actingAs($departmentHead)
            ->post(route('teams.store'), $this->teamPayload($company, $department, $teamLead, [
                'name' => 'Delivery Squad',
                'code' => 'DLV001',
            ]))
            ->assertRedirect(route('teams.index'));

        $team = Team::query()->where('name', 'Delivery Squad')->firstOrFail();

        $this->actingAs($departmentHead)
            ->get(route('teams.edit', $team))
            ->assertOk();

        $this->actingAs($departmentHead)
            ->put(route('teams.update', $team), $this->teamPayload($company, $department, $teamLead, [
                'name' => 'Delivery Squad Updated',
                'code' => 'DLV002',
            ]))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Delivery Squad Updated',
            'code' => 'DLV002',
        ]);

        $this->actingAs($departmentHead)
            ->delete(route('teams.destroy', $team))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_team_lead_can_create_edit_and_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $teamLead = $this->userWithRole($company, $department, UserRole::TEAM_LEAD);

        $this->actingAs($teamLead)
            ->get(route('teams.create'))
            ->assertOk();

        $this->actingAs($teamLead)
            ->post(route('teams.store'), $this->teamPayload($company, $department, $teamLead, [
                'name' => 'Platform Team',
                'code' => 'PLT001',
            ]))
            ->assertRedirect(route('teams.index'));

        $team = Team::query()->where('name', 'Platform Team')->firstOrFail();

        $this->actingAs($teamLead)
            ->get(route('teams.edit', $team))
            ->assertOk();

        $this->actingAs($teamLead)
            ->put(route('teams.update', $team), $this->teamPayload($company, $department, $teamLead, [
                'name' => 'Platform Team Updated',
                'code' => 'PLT002',
            ]))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Platform Team Updated',
            'code' => 'PLT002',
        ]);

        $this->actingAs($teamLead)
            ->delete(route('teams.destroy', $team))
            ->assertRedirect(route('teams.index'));

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_unauthorized_users_cannot_create_edit_or_delete_team(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $generalUser = $this->userWithRole($company, $department, UserRole::GENERAL);
        $admin = $this->userWithRole($company, $department, UserRole::ADMIN);
        $teamLead = $this->userWithRole($company, $department, UserRole::TEAM_LEAD);
        $team = Team::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'team_lead_id' => $teamLead->id,
            'name' => 'Core Team',
            'code' => 'COR001',
        ]);

        foreach ([$generalUser, $admin] as $user) {
            $this->actingAs($user)
                ->get(route('teams.create'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('teams.store'), $this->teamPayload($company, $department, $teamLead, [
                    'name' => 'Blocked Team',
                    'code' => 'BLK001',
                ]))
                ->assertForbidden();

            $this->actingAs($user)
                ->get(route('teams.edit', $team))
                ->assertForbidden();

            $this->actingAs($user)
                ->put(route('teams.update', $team), $this->teamPayload($company, $department, $teamLead, [
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

    private function teamPayload(Company $company, Department $department, User $teamLead, array $overrides = []): array
    {
        return array_merge([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'team_lead_id' => $teamLead->id,
            'member_ids' => [],
            'name' => 'Team Alpha',
            'code' => 'ALP001',
            'status' => '1',
        ], $overrides);
    }

    private function userWithRole(Company $company, Department $department, int $role): User
    {
        $user = User::factory()->forOrganization($company, $department)->create();
        $user->syncRoles([$role]);

        return $user;
    }
}

