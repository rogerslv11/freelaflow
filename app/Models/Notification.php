<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['type', 'title', 'body', 'link', 'icon', 'read'];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
}
