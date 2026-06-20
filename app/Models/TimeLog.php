<?php

namespace App\Models;

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
