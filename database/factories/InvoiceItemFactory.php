<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $qty = fake()->randomFloat(2, 1, 5);
        $price = fake()->randomFloat(2, 200, 5000);
        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->sentence(4),
            'quantity' => $qty,
            'unit_price' => $price,
            'total' => round($qty * $price, 2),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
