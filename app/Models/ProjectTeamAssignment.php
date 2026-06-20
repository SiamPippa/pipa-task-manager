<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTeamAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'team_id',
        'assigned_by',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query
            ->whereHas('project', fn (Builder $projectQuery) => $projectQuery->visibleTo($user))
            ->whereHas('team', fn (Builder $teamQuery) => $teamQuery->visibleTo($user));
    }

    public function isVisibleTo(User $user): bool
    {
        return static::query()->visibleTo($user)->whereKey($this->id)->exists();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
