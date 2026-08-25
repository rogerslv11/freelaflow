<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'name', 'description', 'start_date', 'due_date', 'value',
        'status', 'priority', 'progress', 'color',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'value' => 'decimal:2',
        'progress' => 'integer',
    ];

    public const STATUSES = ['planning', 'in_progress', 'review', 'paused', 'completed', 'cancelled'];

    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
