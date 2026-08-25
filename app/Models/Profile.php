<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use BelongsToUser, HasFactory;

    protected $fillable = [
        'company_name', 'document', 'phone', 'address', 'city', 'state', 'country',
        'postal_code', 'bio', 'avatar', 'timezone', 'currency', 'plan',
        'preferences', 'onboarded',
    ];

    protected $casts = [
        'preferences' => 'array',
        'onboarded' => 'boolean',
    ];

    public const CURRENCIES = ['BRL', 'USD', 'EUR'];

    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency) {
            'USD' => '$',
            'EUR' => '€',
            default => 'R$',
        };
    }
}
