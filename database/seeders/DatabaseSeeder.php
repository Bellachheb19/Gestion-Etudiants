<?php

namespace Database\Seeders;

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
            'first_name' => 'Admin',
            'last_name' => 'S-Gestion',
            'email' => 'test@example.com',
            'role' => 'admin',
            'photo' => 'assets/profiles/admin.png',
            'password' => bcrypt('password')
        ]);
    }
}
