<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-3 months', 'now');
        $end = fake()->dateTimeBetween('now', '+6 months');
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'project_id' => null,
            'title' => 'Contrato ' . fake()->randomElement(['de Prestação de Serviços', 'NDA', 'De Manutenção']),
            'description' => fake()->boolean(60) ? fake()->paragraph(2) : null,
            'value' => fake()->randomFloat(2, 2000, 50000),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'terms' => fake()->paragraphs(3, true),
            'token' => Str::random(40),
            'status' => fake()->randomElement(Contract::STATUSES),
            'signed_at' => fake()->boolean(50) ? fake()->dateTimeBetween('-2 months', 'now') : null,
        ];
    }
}
