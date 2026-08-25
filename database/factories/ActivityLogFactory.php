<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject_type' => fake()->randomElement(['client', 'project', 'invoice']),
            'subject_id' => fake()->numberBetween(1, 50),
            'description' => fake()->sentence(3),
            'properties' => null,
        ];
    }
}
