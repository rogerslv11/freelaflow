<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'title', 'description', 'token', 'discount', 'tax', 'total',
        'valid_until', 'payment_terms', 'notes', 'status',
        'sent_at', 'viewed_at', 'accepted_at', 'rejected_at', 'rejection_reason',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'sent_at' => 'datetime',
        'viewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public const STATUSES = ['draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class)->orderBy('order');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum('total');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->valid_until && $this->valid_until->isPast() && ! in_array($this->status, ['accepted', 'rejected']);
    }
}
