<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Support\Rbac;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'team_lead_id',
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->actingRole() === UserRole::SUPER_ADMIN) {
            return $query;
        }

        $query->where('company_id', $user->company_id);

        if ($user->actingRole() === UserRole::COMPANY_ADMIN) {
            return $query;
        }

        if (Rbac::allows($user, Permission::TEAMS_MANAGE)) {
            return $query;
        }

        if (Rbac::allows($user, Permission::TEAMS_MANAGE_LED)) {
            return $query->where(function (Builder $subQuery) use ($user) {
                $subQuery
                    ->where('team_lead_id', $user->id)
                    ->orWhereHas('members', function (Builder $memberQuery) use ($user) {
                        $memberQuery->where('users.id', $user->id);
                    });
            });
        }

        return $query->whereHas('members', function (Builder $memberQuery) use ($user) {
            $memberQuery->where('users.id', $user->id);
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

    public function teamLead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_lead_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->withPivot(['company_id', 'is_team_lead', 'status'])
            ->withTimestamps();
    }

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(ProjectTeamAssignment::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_team_assignments')
            ->withPivot(['assigned_by', 'assigned_at'])
            ->withTimestamps();
    }
}
