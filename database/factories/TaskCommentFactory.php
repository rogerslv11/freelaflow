<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCommentFactory extends Factory
{
    protected $model = TaskComment::class;

    public function definition(): array
    {
        return [
            'user_id' => fn (array $attrs) => Task::find($attrs['task_id'])->user_id,
            'task_id' => Task::factory(),
            'author' => fake()->name(),
            'body' => fake()->paragraph(2),
        ];
    }
}
