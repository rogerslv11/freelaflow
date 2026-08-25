<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use BelongsToUser, HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'project_id', 'client_id', 'description', 'amount', 'incurred_at', 'note',
    ];

    protected $casts = [
        'incurred_at' => 'date',
        'amount' => 'decimal:2',
    ];

    public const CATEGORIES = ['Software', 'Marketing', 'Equipamentos', 'Transporte', 'Serviços', 'Impostos', 'Outros'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
