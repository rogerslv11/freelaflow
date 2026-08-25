<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', 'now');
        $duration = fake()->numberBetween(1800, 18000);
        $end = (clone $start)->modify("+{$duration} seconds");
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'client_id' => null,
            'task_id' => null,
            'description' => fake()->boolean(60) ? fake()->sentence(3) : null,
            'start_time' => $start->format('Y-m-d H:i:s'),
            'end_time' => $end->format('Y-m-d H:i:s'),
            'duration' => $duration,
            'billable' => fake()->boolean(80),
            'hourly_rate' => fake()->randomElement([80, 100, 120, 150, 200]),
        ];
    }
}
