<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalItem extends Model
{
    use HasFactory;

    protected $fillable = ['proposal_id', 'description', 'quantity', 'unit_price', 'total', 'order'];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
