<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['proposal', 'invoice', 'payment', 'task', 'project']),
            'title' => fake()->sentence(4),
            'body' => fake()->sentence(),
            'link' => '/dashboard',
            'icon' => fake()->randomElement(['bell', 'document', 'currency', 'check', 'clock']),
            'read' => fake()->boolean(40),
        ];
    }
}
