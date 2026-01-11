<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // user - skip if already exists
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
        
        // quiz
        $this->call([
            UserSeeder::class, // Must run first to create teacher/student
            StudentSeeder::class, // Mirror student users into students table with classes
            QuizSeeder::class, // Depends on UserSeeder
            ForumSeeder::class, // Depends on UserSeeder
            LessonSeeder::class, // Depends on UserSeeder
            GameSeeder::class, // Seed games
        ]);
    }
}
