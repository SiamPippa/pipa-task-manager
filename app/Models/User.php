<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\ActiveRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles {
        HasRoles::hasRole as protected spatieHasRole;
        HasRoles::syncRoles as protected spatieSyncRoles;
    }
    use Notifiable;

    protected $fillable = [
        'company_id',
        'employee_id',
        'designation_id',
        'reporting_manager_id',
        'office_location_id',
        'name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporting_manager_id');
    }

    public function directReports(): HasMany
    {
        return $this->hasMany(User::class, 'reporting_manager_id');
    }

    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_lead_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members', 'user_id', 'team_id')
            ->withPivot(['company_id', 'is_team_lead', 'status'])
            ->withTimestamps();
    }

    public function officeLocation(): BelongsTo
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function managedProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_managers')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function assignedTasksBy(): HasMany
    {
        return $this->hasMany(TaskAssignment::class, 'assigned_by');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    /**
     * @return array<int, string>
     */
    public function roleIds(): array
    {
        return $this->getRoleNames()
            ->map(fn (string $role) => UserRole::normalize($role))
            ->values()
            ->all();
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if (is_array($roles)) {
            $roles = array_map(fn ($role) => UserRole::normalize($role), $roles);
        } else {
            $roles = UserRole::normalize($roles);
        }

        return $this->spatieHasRole($roles, $guard);
    }

    public function actingRole(): string
    {
        return ActiveRole::resolve($this);
    }

    public function roleLabel(): string
    {
        return UserRole::label($this->actingRole());
    }

    public function assignedRoleLabels(): string
    {
        return collect($this->roleIds())
            ->map(fn (string $role) => UserRole::label($role))
            ->join(', ');
    }

    /**
     * @param  array<int, string|int>  $roles
     */
    public function syncRoles(array $roles): void
    {
        $roles = array_values(array_unique(array_map(fn ($role) => UserRole::normalize($role), $roles)));

        if ($roles === []) {
            $roles = [UserRole::DEVELOPER];
        }

        if (\Spatie\Permission\Models\Permission::query()->count() === 0) {
            app(\Database\Seeders\RolesAndPermissionsSeeder::class)->run();
        }

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::findOrCreate($role);
        }

        $this->spatieSyncRoles($roles);
        $this->unsetRelation('roles');

        if ($this->is(auth()->user())) {
            $active = session(ActiveRole::SESSION_KEY);

            if ($active === null || ! in_array(UserRole::normalize($active), $roles, true)) {
                ActiveRole::set($this, ActiveRole::defaultFrom($roles));
            }
        }
    }

    public function canManageOrganization(): bool
    {
        return $this->actingCan(\App\Enums\Permission::ORGANIZATION_ACCESS);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->actingCan($permission);
    }

    public function actingCan(string $permission): bool
    {
        if ($this->actingRole() === UserRole::SUPER_ADMIN) {
            return true;
        }

        $role = $this->roles->firstWhere('name', $this->actingRole())
            ?? $this->roles()->where('name', $this->actingRole())->first();

        if (! $role) {
            return false;
        }

        if ($role->hasPermissionTo($permission)) {
            return true;
        }

        return str_ends_with($permission, '.view')
            && $role->permissions->contains(
                fn ($granted) => str_starts_with($granted->name, substr($permission, 0, -5).'.')
            );
    }
}
