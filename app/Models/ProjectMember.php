<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['project_id', 'name', 'email', 'role'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
