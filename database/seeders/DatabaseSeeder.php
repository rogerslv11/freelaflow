<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DemoSeeder::class);

        // Extra isolated users for data-separation testing
        User::factory()->count(2)->create()->each(function ($u) {
            \App\Models\Profile::factory()->create(['user_id' => $u->id, 'onboarded' => true]);
            \App\Models\Client::factory()->count(3)->create(['user_id' => $u->id]);
        });
    }
}
