<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tour;
use App\Models\Travel;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

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

        Travel::factory(30)->create();
        Tour::factory(60)->create();
        Role::factory(2)->create();
    }
}
