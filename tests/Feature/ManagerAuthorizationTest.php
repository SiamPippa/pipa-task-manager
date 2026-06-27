<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\ProjectTeamAssignment;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ManagerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_projects_across_departments_in_same_company(): void
    {
        [$company, $deptA, $deptB, $manager] = $this->managerInDepartment();

        $projectInOtherDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
        ]);

        $this->actingAs($manager);

        $this->assertTrue($projectInOtherDepartment->isVisibleTo($manager));
        $this->assertTrue(Gate::forUser($manager)->allows('view', $projectInOtherDepartment));
    }

    public function test_manager_can_view_but_cannot_update_tasks_in_other_departments(): void
    {
        [$company, $deptA, $deptB, $manager] = $this->managerInDepartment();

        $projectInOtherDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
        ]);

        $task = Task::factory()->create([
            'project_id' => $projectInOtherDepartment->id,
        ]);

        $this->actingAs($manager);

        $this->assertTrue(Gate::forUser($manager)->allows('view', $task));
        $this->assertFalse(Gate::forUser($manager)->allows('update', $task));
        $this->assertFalse(Gate::forUser($manager)->allows('delete', $task));
    }

    public function test_manager_can_update_tasks_in_own_department(): void
    {
        [$company, $deptA, , $manager] = $this->managerInDepartment();

        $projectInOwnDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptA->id,
        ]);

        $task = Task::factory()->create([
            'project_id' => $projectInOwnDepartment->id,
        ]);

        $this->actingAs($manager);

        $this->assertTrue(Gate::forUser($manager)->allows('update', $task));
        $this->assertTrue(Gate::forUser($manager)->allows('delete', $task));
    }

    public function test_manager_index_lists_projects_modules_and_teams_from_all_departments(): void
    {
        [$company, , $deptB, $manager] = $this->managerInDepartment();

        $teamLead = User::factory()->forOrganization($company, $deptB)->create();

        $projectInOtherDepartment = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
            'name' => 'Cross Department Project',
        ]);

        $module = ProjectModule::query()->create([
            'project_id' => $projectInOtherDepartment->id,
            'name' => 'Cross Department Module',
            'details' => null,
            'start_date' => $projectInOtherDepartment->start_date,
            'end_date' => $projectInOtherDepartment->end_date,
            'estimated_hours' => 0,
        ]);

        $team = Team::factory()->create([
            'company_id' => $company->id,
            'department_id' => $deptB->id,
            'team_lead_id' => $teamLead->id,
            'name' => 'Cross Department Team',
        ]);

        $this->actingAs($manager)
            ->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Cross Department Project');

        $this->actingAs($manager)
            ->get(route('project-modules.index'))
            ->assertOk()
            ->assertSee('Cross Department Module');

        $this->actingAs($manager)
            ->get(route('teams.index'))
            ->assertOk()
            ->assertSee('Cross Department Team');

        $this->assertSame($module->id, ProjectModule::query()->visibleTo($manager)->whereKey($module->id)->value('id'));
    }

    public function test_general_user_only_sees_team_assigned_projects_in_department(): void
    {
        $company = Company::factory()->active()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $teamLead = User::factory()->forOrganization($company, $department)->create();
        $teamLead->syncRoles([UserRole::TEAM_LEAD]);

        $generalUser = User::factory()->forOrganization($company, $department)->create();
        $generalUser->syncRoles([UserRole::GENERAL]);

        $team = Team::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'team_lead_id' => $teamLead->id,
        ]);

        $team->members()->attach($generalUser->id, [
            'company_id' => $company->id,
            'department_id' => $department->id,
        ]);

        $assignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
        ]);

        $unassignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
        ]);

        ProjectTeamAssignment::query()->create([
            'project_id' => $assignedProject->id,
            'team_id' => $team->id,
        ]);

        $this->actingAs($generalUser);

        $this->assertTrue($assignedProject->isVisibleTo($generalUser));
        $this->assertFalse($unassignedProject->isVisibleTo($generalUser));
    }

    /**
     * @return array{0: Company, 1: Department, 2: Department, 3: User}
     */
    private function managerInDepartment(): array
    {
        $company = Company::factory()->active()->create();
        $deptA = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Engineering A',
            'code' => 'ENGA',
        ]);
        $deptB = Department::factory()->create([
            'company_id' => $company->id,
            'name' => 'Engineering B',
            'code' => 'ENGB',
        ]);

        $manager = User::factory()->forOrganization($company, $deptA)->create();
        $manager->syncRoles([UserRole::MANAGER]);

        return [$company, $deptA, $deptB, $manager];
    }
}
