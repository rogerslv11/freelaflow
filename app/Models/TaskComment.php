<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskComment extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['task_id', 'author', 'body'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
