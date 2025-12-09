<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // Must run first to create teacher/student
            QuizDataSeeder::class, // Depends on UserSeeder
        ]);
    }
}
