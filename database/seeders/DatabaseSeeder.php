<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        ServiceType::firstOrCreate(
            ['name' => 'Wash & Fold'],
            ['price_per_kg' => 50.00],
        );

        ServiceType::firstOrCreate(
            ['name' => 'Dry Clean'],
            ['price_per_kg' => 120.00],
        );
    }
}
