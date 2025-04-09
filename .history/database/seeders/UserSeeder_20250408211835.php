<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Change 'password' to a secure default
            'role' => 'admin', // Assign the admin role
            'email_verified_at' => now(), // Optionally mark as verified
            // 'organization_id' => null, // Or link to a default org if created
            // 'department_id' => null,
            // 'position_id' => null,
        ]);

        // Optionally create other users (managers, employees) if needed
        // User::factory(10)->create(); // Example using factory
    }
}
