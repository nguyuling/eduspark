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

        $classes = ['1A', '1B', '1C', '2A', '2B', '2C', '3A', '3B'];

        // Create multiple scores per student per game to show real participation
        foreach ($games as $game) {
            foreach ($students as $student) {
                // Create 2-5 score entries per student per game (showing multiple attempts)
                $attempts = rand(2, 5);
                
                for ($i = 0; $i < $attempts; $i++) {
                    Leaderboard::create([
                        'user_id' => $student->id,
                        'username' => $student->name,
                        'class' => $classes[array_rand($classes)], // Random class assignment
                        'game_id' => $game->slug,
                        'score' => rand(150, 1500),
                        'timestamp' => now()->subDays(rand(0, 60))->subHours(rand(0, 24)),
                    ]);
                }
            }
        }

        $this->command->info('LeaderboardSeeder completed successfully! Populated with ' . Leaderboard::count() . ' entries.');
    }
}
