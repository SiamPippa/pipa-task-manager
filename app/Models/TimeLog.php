<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'project_id',
        'task_id',
        'user_id',
        'start_time',
        'end_time',
        'total_minutes',
        'note',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return $query;
        }

        if ($user->actingRole() === UserRole::COMPANY_ADMIN) {
            return $query->whereHas('project', fn (Builder $projectQuery) => $projectQuery->where('company_id', $user->company_id));
        }

        if ($user->actingRole() === UserRole::PROJECT_MANAGER) {
            return $query->whereHas('project.managers', fn (Builder $managerQuery) => $managerQuery->where('users.id', $user->id));
        }

        if ($user->actingRole() === UserRole::TEAM_LEAD) {
            $teamIds = $user->teams()->pluck('teams.id');

            return $query->whereHas('user.teams', fn (Builder $teamQuery) => $teamQuery->whereIn('teams.id', $teamIds));
        }

        return $query->where('user_id', $user->id);
    }

    public function isVisibleTo(User $user): bool
    {
        return static::query()->visibleTo($user)->whereKey($this->id)->exists();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyReport(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class);
    }
}
