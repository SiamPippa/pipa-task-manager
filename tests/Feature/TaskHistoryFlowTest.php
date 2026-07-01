<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectModule;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskHistoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_user_can_update_task_status_from_assignment_view(): void
    {
        [$task, $assignment, $assignee] = $this->taskWithAssignment();

        $response = $this->actingAs($assignee)->patch(
            route('task-assignments.task-status.update', $assignment),
            ['status' => 'in_progress'],
        );

        $response->assertRedirect(route('task-assignments.show', $assignment));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in_progress',
        ]);
        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'actor_id' => $assignee->id,
            'action' => 'task_status_changed',
            'from_status' => 'todo',
            'to_status' => 'in_progress',
        ]);
    }

    public function test_unassigned_user_cannot_update_task_status_from_assignment_view(): void
    {
        [$task, $assignment] = $this->taskWithAssignment();
        $company = Company::query()->findOrFail($task->project->company_id);
        $otherUser = $this->userWithRole($company, UserRole::DEVELOPER);

        $response = $this->actingAs($otherUser)->patch(
            route('task-assignments.task-status.update', $assignment),
            ['status' => 'done'],
        );

        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'todo',
        ]);
    }

    public function test_task_assignment_actions_are_written_to_task_history(): void
    {
        $company = Company::factory()->active()->create();
        $manager = $this->userWithRole($company, UserRole::COMPANY_ADMIN);
        $assigneeA = $this->userWithRole($company, UserRole::DEVELOPER);
        $assigneeB = $this->userWithRole($company, UserRole::DEVELOPER);
        $task = $this->createTask($company, 'todo');

        $store = $this->actingAs($manager)->post(route('task-assignments.store'), [
            'task_id' => $task->id,
            'user_id' => $assigneeA->id,
        ]);
        $store->assertRedirect(route('task-assignments.index'));

        $assignment = TaskAssignment::query()->where('task_id', $task->id)->firstOrFail();

        $update = $this->actingAs($manager)->put(route('task-assignments.update', $assignment), [
            'task_id' => $task->id,
            'user_id' => $assigneeB->id,
        ]);
        $update->assertRedirect(route('task-assignments.index'));

        $delete = $this->actingAs($manager)->delete(route('task-assignments.destroy', $assignment));
        $delete->assertRedirect(route('task-assignments.index'));

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'actor_id' => $manager->id,
            'action' => 'task_assignment_created',
        ]);
        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'actor_id' => $manager->id,
            'action' => 'task_assignment_updated',
        ]);
        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'actor_id' => $manager->id,
            'action' => 'task_assignment_deleted',
        ]);
    }

    private function taskWithAssignment(): array
    {
        $company = Company::factory()->active()->create();
        $task = $this->createTask($company, 'todo');
        $assignee = $this->userWithRole($company, UserRole::DEVELOPER);
        $manager = $this->userWithRole($company, UserRole::COMPANY_ADMIN);

        $assignment = TaskAssignment::query()->create([
            'task_id' => $task->id,
            'user_id' => $assignee->id,
            'assigned_by' => $manager->id,
            'assigned_at' => now(),
        ]);

        return [$task, $assignment, $assignee, $manager];
    }

    private function createTask(Company $company, string $status): Task
    {
        $project = Project::factory()->create([
            'company_id' => $company->id,
        ]);

        $module = ProjectModule::query()->firstOrCreate(
            ['project_id' => $project->id, 'name' => 'General'],
            [
                'details' => null,
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'estimated_hours' => 0,
            ],
        );

        return Task::factory()->create([
            'project_id' => $project->id,
            'project_module_id' => $module->id,
            'status' => $status,
        ]);
    }

    private function userWithRole(Company $company, string|int $role): User
    {
        $user = User::factory()->forOrganization($company)->create(['status' => true]);
        $user->syncRoles([$role]);

        return $user;
    }
}

