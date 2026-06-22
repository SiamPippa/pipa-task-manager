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
        'department_id',
        'name',
        'code',
        'status',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->role === UserRole::ADMIN) {
            return $query;
        }

        if (! $user->company_id || ! $user->department_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('company_id', $user->company_id)
            ->where('department_id', $user->department_id);
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
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
}
