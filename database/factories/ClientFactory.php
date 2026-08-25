<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'company' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'document' => fake()->numerify('###.###.###-##'),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'country' => 'Brasil',
            'notes' => fake()->boolean(40) ? fake()->paragraph(2) : null,
            'status' => fake()->randomElement(Client::STATUSES),
            'color' => fake()->randomElement(['#FF6B00', '#3B82F6', '#10B981', '#A855F7', '#EC4899']),
        ];
    }
}
