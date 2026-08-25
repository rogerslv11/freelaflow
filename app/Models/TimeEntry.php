<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'project_id', 'client_id', 'task_id', 'description',
        'start_time', 'end_time', 'duration', 'billable', 'hourly_rate',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'billable' => 'boolean',
        'duration' => 'integer',
        'hourly_rate' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getBillableAmountAttribute(): float
    {
        if (! $this->billable || ! $this->hourly_rate) {
            return 0;
        }

        return round(($this->duration / 3600) * $this->hourly_rate, 2);
    }
}
