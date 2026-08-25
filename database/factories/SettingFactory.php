<?php

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group' => 'general',
            'key' => fake()->word(),
            'value' => fake()->sentence(),
        ];
    }
}
