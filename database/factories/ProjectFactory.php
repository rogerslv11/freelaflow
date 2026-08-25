<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-3 months', 'now');
        $due = fake()->dateTimeBetween('now', '+3 months');
        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'name' => fake()->randomElement(['Website', 'App Mobile', 'Identidade Visual', 'Landing Page', 'E-commerce', 'Sistema', 'Campanha']) . ' ' . fake()->company(),
            'description' => fake()->boolean(70) ? fake()->paragraph(2) : null,
            'start_date' => $start->format('Y-m-d'),
            'due_date' => $due->format('Y-m-d'),
            'value' => fake()->randomFloat(2, 1500, 45000),
            'status' => fake()->randomElement(Project::STATUSES),
            'priority' => fake()->randomElement(Project::PRIORITIES),
            'progress' => fake()->numberBetween(0, 100),
            'color' => fake()->randomElement(['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899']),
        ];
    }
}
