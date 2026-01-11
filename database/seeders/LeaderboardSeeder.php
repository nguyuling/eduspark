<?php

namespace Database\Seeders;

use App\Models\Leaderboard;
use App\Models\User;
use App\Models\Game;
use Illuminate\Database\Seeder;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::all();
        $students = User::where('role', '!=', 'teacher')->get();
        
        if ($students->isEmpty() || $games->isEmpty()) {
            $this->command->info('No students or games found. Skipping LeaderboardSeeder.');
            return;
        }

        // Form 4 and Form 5 class names
        $classes = ['4A', '4B', '5A', '5B'];

        // Create multiple scores per student per game to show real participation
        foreach ($games as $game) {
            foreach ($students as $student) {
                // Create 2-5 score entries per student per game (showing multiple attempts)
                $attempts = rand(2, 5);
                
                for ($i = 0; $i < $attempts; $i++) {
                    Leaderboard::create([
                        'user_id' => $student->id,
                        'username' => $student->name,
                        'class' => $classes[array_rand($classes)], // Random Form 4 or Form 5 class
                        'game_id' => $game->slug,
                        'score' => rand(150, 1500),
                        'time_taken' => rand(60, 600), // Time in seconds (1 min to 10 min)
                        'timestamp' => now()->subDays(rand(0, 60))->subHours(rand(0, 24)),
                    ]);
                }
            }
        }

        $this->command->info('LeaderboardSeeder completed successfully! Populated with ' . Leaderboard::count() . ' entries.');
    }
}
