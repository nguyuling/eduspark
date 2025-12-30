<?php

namespace Database\Seeders;

use App\Models\GameScore;
use App\Models\User;
use App\Models\Game;
use Illuminate\Database\Seeder;

class GameScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get students (not teachers)
        $students = User::where('role', '!=', 'teacher')->get();
        $games = Game::all();

        if ($students->isEmpty() || $games->isEmpty()) {
            $this->command->info('No students or games found. Skipping GameScoreSeeder.');
            return;
        }

        // Create sample scores for each student and game
        foreach ($games as $game) {
            foreach ($students->random(min(3, $students->count())) as $student) {
                // Create 1-3 score records per student per game
                $scoreCount = rand(1, 3);
                
                for ($i = 0; $i < $scoreCount; $i++) {
                    GameScore::create([
                        'user_id' => $student->id,
                        'game_id' => $game->id,
                        'score' => rand(100, 1000),
                        'time_taken' => rand(60, 600), // seconds
                        'game_stats' => json_encode([
                            'level' => rand(1, 5),
                            'combo' => rand(0, 20),
                            'enemies_defeated' => rand(5, 50),
                        ]),
                        'completed_at' => now()->subDays(rand(0, 30)),
                    ]);
                }
            }
        }

        $this->command->info('GameScoreSeeder completed successfully!');
    }
}
