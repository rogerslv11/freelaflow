<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = ['group', 'key', 'value'];

    public static function getValue($userId, $key, $default = null)
    {
        $setting = static::where('user_id', $userId)->where('key', $key)->first();

        return $setting?->value ?? $default;
    }
}
