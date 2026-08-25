<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'client_id' => null,
            'title' => fake()->sentence(4),
            'description' => fake()->boolean(50) ? fake()->paragraph(2) : null,
            'assignee' => fake()->boolean(60) ? fake()->name() : null,
            'priority' => fake()->randomElement(Task::PRIORITIES),
            'status' => fake()->randomElement(Task::STATUSES),
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d') : null,
            'estimated_hours' => fake()->randomFloat(2, 1, 40),
            'logged_hours' => fake()->randomFloat(2, 0, 30),
            'order' => fake()->numberBetween(0, 100),
        ];
    }

    public function forProject(Project $project): static
    {
        return $this->state(fn () => [
            'user_id' => $project->user_id,
            'project_id' => $project->id,
            'client_id' => $project->client_id,
        ]);
    }
}
