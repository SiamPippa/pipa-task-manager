<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'project_module_id',
        'task_id',
        'report_date',
        'summary',
        'blocker',
        'tomorrow_plan',
        'progress_percent',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return $query;
        }

        if ($user->actingRole() === UserRole::COMPANY_ADMIN && $user->company_id) {
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ProjectModule::class, 'project_module_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function timeLog(): HasOne
    {
        return $this->hasOne(TimeLog::class);
    }
}
