<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+2 months');
        return [
            'user_id' => User::factory(),
            'client_id' => null,
            'project_id' => null,
            'title' => fake()->sentence(3),
            'description' => fake()->boolean(40) ? fake()->sentence() : null,
            'type' => fake()->randomElement(CalendarEvent::TYPES),
            'starts_at' => $start->format('Y-m-d H:i:s'),
            'ends_at' => fake()->boolean(50) ? $start->modify('+1 hour')->format('Y-m-d H:i:s') : null,
            'all_day' => fake()->boolean(30),
            'color' => fake()->randomElement(['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899']),
        ];
    }
}
