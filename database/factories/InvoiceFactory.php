<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $issue = fake()->dateTimeBetween('-2 months', 'now');
        $due = (clone $issue)->modify('+'.fake()->numberBetween(7, 30).' days');
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'project_id' => null,
            'number' => 'INV-' . fake()->unique()->numerify('####'),
            'issue_date' => $issue->format('Y-m-d'),
            'due_date' => $due->format('Y-m-d'),
            'discount' => fake()->randomFloat(2, 0, 300),
            'tax' => fake()->randomFloat(2, 0, 400),
            'total' => fake()->randomFloat(2, 500, 25000),
            'status' => fake()->randomElement(Invoice::STATUSES),
            'token' => Str::random(40),
            'note' => fake()->boolean(30) ? fake()->sentence() : null,
            'sent_at' => fake()->boolean(60) ? $issue : null,
            'paid_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
