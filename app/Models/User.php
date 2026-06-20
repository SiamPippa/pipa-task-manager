<?php

namespace App\Models;

use App\Enums\UserRole;
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
        'role',
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
        'role' => 'integer',
    ];

    protected $attributes = [
        'role' => UserRole::GENERAL,
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

    public function roleLabel(): string
    {
        return UserRole::label($this->role);
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
