<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'project_id' => null,
            'invoice_id' => null,
            'amount' => fake()->randomFloat(2, 200, 15000),
            'paid_at' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'method' => fake()->randomElement(Payment::METHODS),
            'note' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
