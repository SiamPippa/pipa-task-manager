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
        if ($user->actingRole() === UserRole::ADMIN) {
            return $query;
        }

        if ($user->actingRole() === UserRole::DEPARTMENT_HEAD && $user->department_id) {
            return $query->where(function (Builder $reportQuery) use ($user) {
                $reportQuery
                    ->whereHas('project', fn (Builder $projectQuery) => $projectQuery->where('department_id', $user->department_id))
                    ->orWhereHas('user', fn (Builder $userQuery) => $userQuery->where('department_id', $user->department_id));
            });
        }

        if (in_array($user->actingRole(), [UserRole::TEAM_PRODUCT_MANAGER, UserRole::TEAM_LEAD], true)) {
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
