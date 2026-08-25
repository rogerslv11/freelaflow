<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id', 'client_id', 'title', 'description', 'assignee',
        'priority', 'status', 'due_date', 'estimated_hours', 'logged_hours', 'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'estimated_hours' => 'decimal:2',
        'logged_hours' => 'decimal:2',
        'order' => 'integer',
    ];

    public const STATUSES = ['todo', 'in_progress', 'review', 'done'];

    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function scopeKanban($query)
    {
        return $query->orderBy('order');
    }
}
