<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'project_id', 'number', 'issue_date', 'due_date',
        'discount', 'tax', 'total', 'status', 'token', 'note',
        'sent_at', 'paid_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public const STATUSES = ['draft', 'sent', 'pending', 'paid', 'overdue', 'cancelled'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return in_array($this->status, ['sent', 'pending']) && $this->due_date && $this->due_date->isPast();
    }
}
