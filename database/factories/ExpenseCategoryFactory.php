<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->randomElement(['Software', 'Marketing', 'Equipamentos', 'Transporte', 'Serviços', 'Impostos', 'Outros']),
            'color' => fake()->randomElement(['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899']),
        ];
    }
}
