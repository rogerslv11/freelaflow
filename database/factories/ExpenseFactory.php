<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => ExpenseCategory::factory(),
            'project_id' => null,
            'client_id' => null,
            'description' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 20, 3000),
            'incurred_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'note' => fake()->boolean(30) ? fake()->sentence() : null,
        ];
    }
}
