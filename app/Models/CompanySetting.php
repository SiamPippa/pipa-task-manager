<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'office_start_time',
        'office_end_time',
        'working_hours_per_day',
        'allow_manual_time_log',
        'require_daily_report',
    ];

    protected $casts = [
        'allow_manual_time_log' => 'boolean',
        'require_daily_report' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
