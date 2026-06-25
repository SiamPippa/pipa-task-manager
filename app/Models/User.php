<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\ActiveRole;
use App\Support\Rbac;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'department_id',
        'designation_id',
        'reporting_manager_id',
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRoleAssignment::class);
    }

    public function ledTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'team_lead_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members', 'user_id', 'team_id')
            ->withPivot(['company_id', 'department_id'])
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
     * @return array<int, int>
     */
    public function roleIds(): array
    {
        if ($this->relationLoaded('userRoles')) {
            return $this->userRoles->pluck('role')->map(fn ($role) => (int) $role)->all();
        }

        return $this->userRoles()->pluck('role')->map(fn ($role) => (int) $role)->all();
    }

    public function hasRole(int $role): bool
    {
        return in_array($role, $this->roleIds(), true);
    }

    public function actingRole(): int
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
            ->map(fn (int $role) => UserRole::label($role))
            ->join(', ');
    }

    /**
     * @param  array<int, int>  $roles
     */
    public function syncRoles(array $roles): void
    {
        $roles = array_values(array_unique(array_map('intval', $roles)));

        if ($roles === []) {
            $roles = [UserRole::GENERAL];
        }

        $this->userRoles()->whereNotIn('role', $roles)->delete();

        foreach ($roles as $role) {
            $this->userRoles()->firstOrCreate(['role' => $role]);
        }

        $this->unsetRelation('userRoles');

        if ($this->is(auth()->user())) {
            $active = session(ActiveRole::SESSION_KEY);

            if ($active === null || ! in_array((int) $active, $roles, true)) {
                ActiveRole::set($this, min($roles));
            }
        }
    }

    public function canManageOrganization(): bool
    {
        return Rbac::canManageOrganization($this);
    }

    public function hasPermission(string $permission): bool
    {
        return Rbac::allows($this, $permission);
    }
}
