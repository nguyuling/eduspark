<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameProgress;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameProgressController extends Controller
{
    // Start or resume game
    public function startGame(Game $game)
    {
        $user = Auth::user() ?? User::find(1); // For testing, use guest user
        
        $progress = GameProgress::firstOrCreate(
            ['user_id' => $user->id, 'game_id' => $game->id],
            ['level' => 1, 'attempts' => 0, 'score' => 0]
        );

        return response()->json([
            'message' => 'Game started successfully',
            'progress' => $progress,
            'current_level' => $progress->level
        ]);
    }

    // Save game progress
    public function saveProgress(Request $request, Game $game)
    {
        $request->validate([
            'score' => 'required|integer',
            'level' => 'required|integer',
            'time_spent' => 'required|integer',
            'completed' => 'boolean',
            'progress_data' => 'nullable|array'
        ]);

        $user = Auth::user() ?? User::find(1);
        
        $progress = GameProgress::updateOrCreate(
            ['user_id' => $user->id, 'game_id' => $game->id],
            [
                'score' => $request->score,
                'level' => $request->level,
                'time_spent' => $request->time_spent,
                'attempts' => \DB::raw('attempts + 1'),
                'last_played_at' => now(),
                'progress_data' => $request->progress_data
            ]
        );

        // Update highest score
        if ($request->score > $progress->highest_score) {
            $progress->update(['highest_score' => $request->score]);
        }

        // Mark as completed if finished
        if ($request->completed) {
            $progress->update(['completed' => true]);
            
            // Check for rewards
            $this->checkRewards($user, $game, $request->score);
        }

        // Calculate stars (1-3 based on performance)
        $stars = $this->calculateStars($request->score, $game->difficulty);
        $progress->update(['stars' => $stars]);

        return response()->json([
            'message' => 'Progress saved successfully',
            'progress' => $progress,
            'stars_earned' => $stars,
            'rewards_unlocked' => $this->getUnlockedRewards($user, $game)
        ]);
    }

    // Get user's game progress
    public function getProgress(Game $game)
    {
        $user = Auth::user() ?? User::find(1);
        
        $progress = GameProgress::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->first();

        $rewards = $user->rewards()->where('game_id', $game->id)->get();

        return response()->json([
            'progress' => $progress,
            'rewards' => $rewards,
            'accuracy' => $progress ? $progress->accuracy : 0
        ]);
    }

    // Get all user progress (for teacher view)
    public function getAllProgress()
    {
        $progress = GameProgress::with(['game', 'user'])
            ->orderBy('highest_score', 'DESC')
            ->get()
            ->groupBy('game_id');

        return response()->json($progress);
    }

    // Claim a reward
    public function claimReward(Reward $reward)
    {
        $user = Auth::user() ?? User::find(1);
        
        $userReward = $user->rewards()->where('reward_id', $reward->id)->first();
        
        if ($userReward && !$userReward->pivot->claimed) {
            $user->rewards()->updateExistingPivot($reward->id, [
                'claimed' => true,
                'claimed_at' => now()
            ]);

            return response()->json([
                'message' => 'Reward claimed successfully!',
                'reward' => $reward
            ]);
        }

        return response()->json(['message' => 'Reward already claimed or not earned'], 400);
    }

    private function calculateStars($score, $difficulty)
    {
        $maxScores = [
            'easy' => 100,
            'medium' => 200,
            'hard' => 300
        ];

        $maxScore = $maxScores[$difficulty] ?? 100;
        $percentage = ($score / $maxScore) * 100;

        if ($percentage >= 80) return 3;
        if ($percentage >= 50) return 2;
        return 1;
    }

    private function checkRewards($user, $game, $score)
    {
        $rewards = Reward::where('game_id', $game->id)
            ->orWhereNull('game_id')
            ->get();

        foreach ($rewards as $reward) {
            // Check if user qualifies for this reward
            if ($this->qualifiesForReward($user, $reward, $game, $score)) {
                // Attach reward if not already earned
                if (!$user->rewards()->where('reward_id', $reward->id)->exists()) {
                    $user->rewards()->attach($reward->id, [
                        'earned_at' => now(),
                        'claimed' => false
                    ]);
                }
            }
        }
    }

    private function qualifiesForReward($user, $reward, $game, $score)
    {
        $progress = GameProgress::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->first();

        if ($reward->game_id && $reward->game_id != $game->id) {
            return false;
        }

        if ($reward->points_required > 0 && $score < $reward->points_required) {
            return false;
        }

        if ($reward->level_required && $progress && $progress->level < $reward->level_required) {
            return false;
        }

        return true;
    }

    private function getUnlockedRewards($user, $game)
    {
        return $user->rewards()
            ->where('game_id', $game->id)
            ->wherePivot('claimed', false)
            ->get();
    }
}