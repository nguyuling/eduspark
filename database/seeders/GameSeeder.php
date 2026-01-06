<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first teacher user (or create one if needed)
        $teacher = User::where('role', 'teacher')->first();
        
        if (!$teacher) {
            $teacher = User::first();
        }

        if (!$teacher) {
            return; // Skip if no users exist
        }

        $games = [
            [
                'title' => 'Cosmic Defender',
                'slug' => 'cosmic-defender',
                'description' => 'Space adventure game where you defend your spaceship from alien invaders',
                'difficulty' => 'medium',
                'category' => 'Action',
                'game_type' => 'arcade',
                'topic' => 'action',
                'is_published' => true,
            ],
            [
                'title' => 'Whack-a-Mole',
                'slug' => 'whack-a-mole',
                'description' => 'Classic whack-a-mole game to test your reflexes',
                'difficulty' => 'easy',
                'category' => 'Casual',
                'game_type' => 'arcade',
                'topic' => 'casual',
                'is_published' => true,
            ],
            [
                'title' => 'Memory Match',
                'slug' => 'memory-match',
                'description' => 'Challenge your memory by matching pairs of cards',
                'difficulty' => 'easy',
                'category' => 'Puzzle',
                'game_type' => 'memory',
                'topic' => 'puzzle',
                'is_published' => true,
            ],
            [
                'title' => 'Maze Quest',
                'slug' => 'maze-game',
                'description' => 'Navigate through mazes and answer programming questions',
                'difficulty' => 'medium',
                'category' => 'Education',
                'game_type' => 'adventure',
                'topic' => 'programming',
                'is_published' => true,
            ],
            [
                'title' => 'Quiz Challenge',
                'slug' => 'quiz-challenge',
                'description' => 'Test your knowledge with quick quiz challenges',
                'difficulty' => 'hard',
                'category' => 'Education',
                'game_type' => 'quiz',
                'topic' => 'general',
                'is_published' => true,
            ],
        ];

        foreach ($games as $game) {
            Game::updateOrCreate(
                ['slug' => $game['slug']],
                array_merge($game, ['teacher_id' => $teacher->id])
            );
        }
    }
}
