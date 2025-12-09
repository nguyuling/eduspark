<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a Teacher User
        User::create([
            'name' => 'Ms. Computer Science',
            'email' => 'teacher@school.com',
            'password' => Hash::make('password'), // Use 'password' for easy testing
            'role' => 'teacher',
        ]);

        // Create a Student User
        User::create([
            'name' => 'Student Test User',
            'email' => 'student@school.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);
    }
}
