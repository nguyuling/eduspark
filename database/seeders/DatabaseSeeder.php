<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // quiz
        $this->call([
            UserSeeder::class, // Must run first to create teacher/student
            QuizSeeder::class, // Depends on UserSeeder
            ForumSeeder::class, // Depends on UserSeeder
            LessonSeeder::class, // Depends on UserSeeder
        ]);
    }
}
