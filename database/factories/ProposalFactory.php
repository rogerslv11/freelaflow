<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'title' => 'Proposta ' . fake()->randomElement(['Website', 'App', 'Branding', 'Marketing']),
            'description' => fake()->boolean(60) ? fake()->paragraph(3) : null,
            'token' => Str::random(40),
            'discount' => fake()->randomFloat(2, 0, 500),
            'tax' => fake()->randomFloat(2, 0, 800),
            'total' => fake()->randomFloat(2, 1000, 30000),
            'valid_until' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'payment_terms' => fake()->randomElement(['50% na assinatura, 50% na entrega', '100% antecipado', 'Parcelado em 3x']),
            'notes' => fake()->boolean(40) ? fake()->sentence() : null,
            'status' => fake()->randomElement(Proposal::STATUSES),
            'sent_at' => fake()->boolean(50) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
