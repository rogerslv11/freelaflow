<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['subject_type', 'subject_id', 'description', 'properties'];

    protected $casts = [
        'properties' => 'array',
    ];
}
