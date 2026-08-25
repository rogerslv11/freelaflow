<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'document' => fake()->numerify('##.###.###/####-##'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => 'Brasil',
            'postal_code' => fake()->postcode(),
            'bio' => fake()->sentence(),
            'timezone' => 'America/Sao_Paulo',
            'currency' => fake()->randomElement(['BRL', 'USD', 'EUR']),
            'plan' => 'pro',
            'onboarded' => true,
        ];
    }
}
