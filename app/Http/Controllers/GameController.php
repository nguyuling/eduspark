<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use App\Models\Leaderboard;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class GameController extends Controller
{
    /**
     * Display games page (student view shows games to play, teacher view shows management)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Game::query();
        
        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        
        // Filter by game type
        if ($request->filled('game_type')) {
            $query->where('game_type', $request->game_type);
        }
        
        if ($user && $user->role === 'teacher') {
            // Teacher view - show ALL games (all teachers can manage all games)
            $games = $query->get();
        } else {
            // Student view - show published games only
            $games = $query->where('is_published', true)->get();
        }
        
        // Get unique values for filters
        $categories = Game::distinct()->pluck('category')->filter();
        $gameTypes = Game::distinct()->pluck('game_type')->filter();
        
        return view('games.index', compact('games', 'categories', 'gameTypes'));
    }

    /**
     * Store updated game
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
        ]);

        $game->update($validated);

        return back()->with('success', 'Game updated successfully!');
    }

    /**
     * Delete game (soft delete)
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return back()
            ->with('success_undo', "Game '{$game->title}' deleted! You can undo this action.")
            ->with('undo_game_id', $id);
    }

    /**
     * Restore soft-deleted game
     */
    public function restore($id)
    {
        $game = Game::withTrashed()->findOrFail($id);
        $game->restore();

        return back()
            ->with('success', 'Game restored successfully!');
    }

    /**
     * Play a game - loads the game blade file
     */
    public function play($id)
    {
        $game = Game::findOrFail($id);
        
        // Get the game blade file path based on slug
        $gameView = 'games.play.' . $game->slug;
        
        if (!view()->exists($gameView)) {
            return back()->with('error', 'Game files not found.');
        }
        
        return view($gameView, compact('game'));
    }

    /**
     * Store game result/score after student completes a game
     */
    public function storeResult(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        $user = auth()->user();

        $validated = $request->validate([
            'score' => 'required|integer|min:0',
            'time_taken' => 'required|integer|min:0',
        ]);

        // Save to GameScore
        $gameScore = GameScore::create([
            'user_id' => $user->id,
            'game_id' => $id,
            'score' => $validated['score'],
            'time_taken' => $validated['time_taken'],
            'completed_at' => now(),
        ]);

        // Also save to leaderboard table if it exists
        if (Schema::hasTable('leaderboard')) {
            // Get student class from students table
            $studentClass = 'Unknown';
            if ($user->role === 'student') {
                $student = DB::table('students')->where('user_id', $user->id)->first();
                if ($student && $student->class) {
                    $studentClass = $student->class;
                }
            }
            
            Leaderboard::create([
                'user_id' => $user->id,
                'username' => $user->name,
                'class' => $studentClass,
                'game_id' => $game->slug,
                'score' => $validated['score'],
                'time_taken' => $validated['time_taken'],
                'timestamp' => now(),
            ]);
        }

        // Award rewards based on performance
        $rewards = $this->calculateRewards($game, $validated['score'], $validated['time_taken']);
        $createdRewardIds = [];
        
        foreach ($rewards as $rewardData) {
            $reward = Reward::create([
                'user_id' => $user->id,
                'game_id' => $id,
                'reward_type' => $rewardData['type'],
                'reward_name' => $rewardData['name'],
                'reward_description' => $rewardData['description'],
                'points_awarded' => $rewardData['points'],
                'badge_icon' => $rewardData['icon'],
                'is_claimed' => false,
            ]);

            $createdRewardIds[] = $reward->id;
        }

        // Store result in session for the result view
        session([
            'game_result' => [
                'game_id' => $id,
                'game_slug' => $game->slug,
                'game_title' => $game->title,
                'score' => $validated['score'],
                'time_taken' => $validated['time_taken'],
                'rewards' => $rewards,
                'reward_ids' => $createdRewardIds,
            ]
        ]);

        return redirect()->route('games.result', $id);
    }

    /**
     * Show game result screen
     */
    public function result($id)
    {
        $game = Game::findOrFail($id);
        $result = session('game_result');

        if (!$result || $result['game_id'] != $id) {
            return redirect()->route('games.index')->with('error', 'No game result found.');
        }

        $rewardRecords = collect();

        // If we have reward IDs from the session, load those specific rewards to reflect claim status
        if (!empty($result['reward_ids'])) {
            $rewardRecords = Reward::whereIn('id', $result['reward_ids'])
                ->where('user_id', auth()->id())
                ->get();
        } else {
            // Fallback: get latest rewards for this game and user (last 10 records)
            $rewardRecords = Reward::where('user_id', auth()->id())
                ->where('game_id', $id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('games.result', compact('game', 'result', 'rewardRecords'));
    }

    /**
     * Get leaderboard for a game
     */
    public function leaderboard(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        $scores = collect();
        $currentUser = auth()->user();
        $highlightedUserIndex = -1;
        $classFilter = $request->query('class');
        $classes = collect();
        
        // First, try to get from existing leaderboard table if it exists
        if (Schema::hasTable('leaderboard')) {
            $classQuery = Leaderboard::where('game_id', $game->slug);

            if (!empty($classFilter)) {
                $classQuery->where('class', $classFilter);
            }

            $leaderboardEntries = $classQuery
                ->orderBy('score', 'desc')
                ->orderBy('timestamp', 'desc')
                ->get();
            
            // Available classes for filter dropdown
            $classes = Leaderboard::where('game_id', $game->slug)
                ->distinct()
                ->pluck('class')
                ->filter();
            
            if ($leaderboardEntries->isNotEmpty()) {
                // Transform leaderboard data to match our view structure
                $index = 0;
                foreach ($leaderboardEntries as $entry) {
                    $score = (object)[
                        'user_id' => $entry->user_id,
                        'score' => $entry->score,
                        'time_taken' => $entry->time_taken,
                        'completed_at' => $entry->timestamp,
                        'user' => (object)[
                            'name' => $entry->username,
                            'email' => $entry->class,
                        ],
                    ];
                    $scores->push($score);
                    
                    // Track the current user's first entry (latest score)
                    if ($entry->user_id === $currentUser->id && $highlightedUserIndex === -1) {
                        $highlightedUserIndex = $index;
                    }
                    $index++;
                }
            }
        }
        
        // Fallback to GameScore table if no leaderboard entries found
        if ($scores->isEmpty()) {
            $scores = GameScore::where('game_id', $id)
                ->with('user')
                ->orderBy('score', 'desc')
                ->orderBy('completed_at', 'desc')
                ->get();
            
            // Track current user's index in fallback
            $index = 0;
            foreach ($scores as $score) {
                if ($score->user_id === $currentUser->id && $highlightedUserIndex === -1) {
                    $highlightedUserIndex = $index;
                    break;
                }
                $index++;
            }
        }
        
        return view('games.leaderboard', compact('game', 'scores', 'currentUser', 'highlightedUserIndex', 'classes', 'classFilter'));
    }

    /**
     * Calculate rewards based on game performance
     */
    private function calculateRewards($game, $score, $timeTaken)
    {
        $rewards = [];
        
        // Score-based rewards
        if ($score >= 1000) {
            $rewards[] = [
                'type' => 'badge',
                'name' => 'Master Player',
                'description' => 'Scored 1000+ points!',
                'points' => 100,
                'icon' => '🏆',
            ];
        } elseif ($score >= 500) {
            $rewards[] = [
                'type' => 'badge',
                'name' => 'Expert Player',
                'description' => 'Scored 500+ points!',
                'points' => 50,
                'icon' => '⭐',
            ];
        } elseif ($score >= 100) {
            $rewards[] = [
                'type' => 'badge',
                'name' => 'Great Player',
                'description' => 'Scored 100+ points!',
                'points' => 25,
                'icon' => '🌟',
            ];
        }

        // Time-based rewards (fast completion)
        if ($timeTaken <= 60) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Speed Demon',
                'description' => 'Completed in under 1 minute!',
                'points' => 50,
                'icon' => '⚡',
            ];
        } elseif ($timeTaken <= 180) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Quick Thinker',
                'description' => 'Completed in under 3 minutes!',
                'points' => 25,
                'icon' => '💨',
            ];
        }

        // Completion reward (everyone gets this)
        $rewards[] = [
            'type' => 'points',
            'name' => 'Game Completed',
            'description' => 'Finished the game!',
            'points' => 10,
            'icon' => '✅',
        ];

        return $rewards;
    }

    /**
     * Claim a reward
     */
    public function claimReward($id)
    {
        $reward = Reward::findOrFail($id);
        
        // Check if the reward belongs to the current user
        if ($reward->user_id !== auth()->id()) {
            return back()->with('error', 'This reward does not belong to you.');
        }

        // Check if already claimed
        if ($reward->is_claimed) {
            return back()->with('error', 'This reward has already been claimed.');
        }

        // Claim the reward
        $reward->update([
            'is_claimed' => true,
            'claimed_at' => now(),
        ]);

        return back()->with('success', 'Reward claimed successfully! You earned ' . $reward->points_awarded . ' points!');
    }

    /**
     * View all rewards for current user
     */
    public function myRewards()
    {
        $user = auth()->user();
        $rewards = Reward::where('user_id', $user->id)
            ->with('game')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPoints = $rewards->where('is_claimed', true)->sum('points_awarded');
        $unclaimedCount = $rewards->where('is_claimed', false)->count();

        return view('games.rewards', compact('rewards', 'totalPoints', 'unclaimedCount'));
    }
}

