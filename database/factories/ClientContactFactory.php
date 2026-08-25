<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientContactFactory extends Factory
{
    protected $model = ClientContact::class;

    public function definition(): array
    {
        return [
            'user_id' => fn (array $attrs) => Client::find($attrs['client_id'])->user_id,
            'client_id' => Client::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'role' => fake()->jobTitle(),
        ];
    }
}
