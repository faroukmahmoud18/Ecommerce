<?php

namespace Database\Seeders; // Assuming namespace

use Illuminate\Database\Seeder;
use App\Models\User; // Assuming model namespace
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // For truncate if needed

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate(); // Optional: Clear existing users

        // Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // More secure default
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Regular Users
        User::factory()->create([
            'name' => 'Regular User One',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        User::factory()->count(3)->create([
            'role' => 'user',
            'status' => 'active',
        ]); // Creates 3 more regular users
    }
}
