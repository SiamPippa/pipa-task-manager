<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'client_name',
        'description',
        'start_date',
        'end_date',
        'estimated_hours',
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_hours' => 'decimal:2',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return $query;
        }

        if (! $user->company_id) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('company_id', $user->company_id);

        if ($user->actingRole() === UserRole::COMPANY_ADMIN) {
            return $query;
        }

        if ($user->actingRole() === UserRole::PROJECT_MANAGER) {
            return $query->whereHas('managers', fn (Builder $managerQuery) => $managerQuery->where('users.id', $user->id));
        }

        if (in_array($user->actingRole(), [UserRole::TEAM_LEAD, UserRole::DEVELOPER, UserRole::QA, UserRole::VIEWER], true)) {
            return $query->assignedToUserTeams($user);
        }

        return $query;
    }

    public function scopeAssignedToUserTeams(Builder $query, User $user): Builder
    {
        return $query->whereHas('projectTeamAssignments', function (Builder $assignmentQuery) use ($user) {
            $assignmentQuery->whereHas('team.members', function (Builder $memberQuery) use ($user) {
                $memberQuery->where('users.id', $user->id);
            });
        });
    }

    public function isVisibleTo(User $user): bool
    {
        return static::query()->visibleTo($user)->whereKey($this->id)->exists();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projectTeamAssignments(): HasMany
    {
        return $this->hasMany(ProjectTeamAssignment::class);
    }

    public function assignedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'project_team_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_managers')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ProjectModule::class);
    }
}
