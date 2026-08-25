<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\ProposalItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalItemFactory extends Factory
{
    protected $model = ProposalItem::class;

    public function definition(): array
    {
        $qty = fake()->randomFloat(2, 1, 5);
        $price = fake()->randomFloat(2, 200, 5000);
        return [
            'proposal_id' => Proposal::factory(),
            'description' => fake()->sentence(4),
            'quantity' => $qty,
            'unit_price' => $price,
            'total' => round($qty * $price, 2),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
