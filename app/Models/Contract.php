<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id', 'project_id', 'title', 'description', 'value',
        'start_date', 'end_date', 'terms', 'token', 'status', 'signed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'decimal:2',
        'signed_at' => 'datetime',
    ];

    public const STATUSES = ['draft', 'active', 'ended', 'cancelled'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
