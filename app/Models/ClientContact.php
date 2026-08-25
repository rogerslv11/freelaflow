<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientContact extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['client_id', 'name', 'email', 'phone', 'role'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
