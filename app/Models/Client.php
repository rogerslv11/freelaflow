<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'company', 'email', 'phone', 'whatsapp', 'document',
        'address', 'city', 'state', 'country', 'notes', 'status', 'color',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public const STATUSES = ['active', 'inactive', 'lead'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClientContact::class);
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('');
    }
}
