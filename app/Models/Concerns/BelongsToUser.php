<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToUser
{
    protected static function bootBelongsToUser(): void
    {
        static::addGlobalScope(new UserScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->withoutGlobalScope(UserScope::class)->where('user_id', $userId);
    }
}
