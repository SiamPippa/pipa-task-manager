<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'jira_task_no',
        'title',
        'description',
        'estimate_hours',
        'status',
    ];

    protected $casts = [
        'estimate_hours' => 'decimal:2',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->whereHas('project', fn (Builder $projectQuery) => $projectQuery->visibleTo($user));
    }

    public function isVisibleTo(User $user): bool
    {
        return static::query()->visibleTo($user)->whereKey($this->id)->exists();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
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
