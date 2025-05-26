<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default staff user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'role' => User::ROLE_STAFF,
                'password' => Hash::make('password'),
            ]
        );

        // Create a default doctor user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'doctor@example.com'],
            [
                'name' => 'Dr. Smith',
                'role' => User::ROLE_DOCTOR,
                'password' => Hash::make('password'),
            ]
        );

        // Update existing admin user to have staff role
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser && !$adminUser->role) {
            $adminUser->update(['role' => User::ROLE_STAFF]);
        }
    }
} 