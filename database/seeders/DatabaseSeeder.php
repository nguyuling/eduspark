<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            QuizDataSeeder::class, // Depends on UserSeeder
            ForumPostSeeder::class, // Depends on UserSeeder
        ]);
    }
}
