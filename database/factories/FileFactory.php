<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        $ext = fake()->randomElement(['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png', 'zip']);
        return [
            'user_id' => User::factory(),
            'client_id' => null,
            'project_id' => null,
            'name' => fake()->slug() . '.' . $ext,
            'original_name' => fake()->word() . '.' . $ext,
            'path' => 'uploads/' . fake()->sha1() . '.' . $ext,
            'mime_type' => fake()->mimeType(),
            'extension' => $ext,
            'size' => fake()->numberBetween(10000, 8000000),
            'folder' => 'root',
            'uploaded_by' => fake()->name(),
        ];
    }
}
