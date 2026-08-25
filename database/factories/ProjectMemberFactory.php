<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectMemberFactory extends Factory
{
    protected $model = ProjectMember::class;

    public function definition(): array
    {
        return [
            'user_id' => fn (array $attrs) => Project::find($attrs['project_id'])->user_id,
            'project_id' => Project::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'role' => fake()->randomElement(['Designer', 'Dev', 'Copywriter', 'QA']),
        ];
    }
}
