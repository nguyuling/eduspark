<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Display list of games for students
     */
    public function index()
    {
        $games = Game::where('is_published', true)
            ->where('is_active', true)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return view('games.index', compact('games'));
    }

    /**
     * Display a single game
     */
    public function show($id)
    {
        $game = Game::with('creator')->findOrFail($id);
        
        // Check if game is published or user is teacher/admin
        if (!$game->is_published && (!Auth::check() || Auth::user()->role !== 'teacher')) {
            abort(404);
        }
        
        return view('games.show', compact('game'));
    }

    /**
     * Get games for API (AJAX calls)
     */
    public function apiIndex()
    {
        $games = Game::where('is_published', true)
            ->where('is_active', true)
            ->select('id', 'title', 'description', 'difficulty', 'category', 'cover_image')
            ->get();
        
        return response()->json([
            'success' => true,
            'games' => $games
        ]);
    }

    /**
     * Get single game for API
     */
    public function apiShow($id)
    {
        $game = Game::with('creator')->find($id);
        
        if (!$game) {
            return response()->json([
                'success' => false,
                'message' => 'Game not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'game' => $game
        ]);
    }

    /**
     * Save game score
     */
    public function saveScore(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'user_id' => 'required',
            'game_id' => 'required|integer',
            'score' => 'required|integer',
            'time_taken' => 'integer|nullable',
            'game_stats' => 'nullable'
        ]);
        
        // Create the score record
        $gameScore = GameScore::create([
            'user_id' => $validated['user_id'],
            'game_id' => $validated['game_id'],
            'score' => $validated['score'],
            'time_taken' => $validated['time_taken'] ?? 0,
            'game_stats' => is_array($validated['game_stats'] ?? []) 
                ? json_encode($validated['game_stats']) 
                : ($validated['game_stats'] ?? '{}')
        ]);
        
        // Log the score save for debugging
        \Log::info('Game score saved', [
            'game_id' => $gameScore->game_id,
            'user_id' => $gameScore->user_id,
            'score' => $gameScore->score,
            'score_id' => $gameScore->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully',
            'score_id' => $gameScore->id,
            'data' => [
                'game_id' => $gameScore->game_id,
                'user_id' => $gameScore->user_id,
                'score' => $gameScore->score,
                'time_taken' => $gameScore->time_taken
            ]
        ]);
    }

    /**
     * Get game summary after completion
     */
    public function getGameSummary($gameId)
    {
        try {
            // Get user ID from request or use test user for development
            $userId = request()->input('user_id');
            
            // If no user_id provided in request, try to get authenticated user
            if (!$userId && Auth::check()) {
                $userId = Auth::id();
            }
            
            // If still no user ID, create a test user ID for development
            if (!$userId) {
                $userId = 'test_user_' . $gameId . '_' . time();
            }
            
            \Log::info('Getting game summary', [
                'game_id' => $gameId,
                'user_id' => $userId,
                'auth_check' => Auth::check()
            ]);
            
            // Try to get the latest score for this game and user
            $latestScore = GameScore::where('user_id', $userId)
                ->where('game_id', $gameId)
                ->latest()
                ->first();
            
            // If no score exists, check if we have any scores for this game at all
            if (!$latestScore) {
                $anyScores = GameScore::where('game_id', $gameId)->exists();
                
                if ($anyScores) {
                    // User hasn't played this game yet
                    return response()->json([
                        'success' => false,
                        'message' => 'No game data found for this user',
                        'has_game_data' => false
                    ]);
                } else {
                    // No scores exist for this game at all - create test data
                    $latestScore = new GameScore([
                        'user_id' => $userId,
                        'game_id' => $gameId,
                        'score' => rand(500, 1000),
                        'time_taken' => rand(30, 180),
                        'game_stats' => json_encode([
                            'correct' => rand(8, 10),
                            'total' => 10,
                            'accuracy' => rand(80, 100),
                            'questions' => [
                                ['question' => 'Q1', 'correct' => true, 'time' => 5],
                                ['question' => 'Q2', 'correct' => true, 'time' => 7],
                                ['question' => 'Q3', 'correct' => false, 'time' => 10],
                            ]
                        ]),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Save test score if we're in development
                    if (app()->environment('local')) {
                        $latestScore->save();
                    }
                }
            }
            
            // Calculate rank - users with higher scores
            $rank = GameScore::where('game_id', $gameId)
                ->where('score', '>', $latestScore->score)
                ->distinct('user_id')
                ->count() + 1;
            
            // Get total unique players
            $totalPlayers = GameScore::where('game_id', $gameId)
                ->distinct('user_id')
                ->count();
            
            // Calculate rewards
            $rewards = $this->calculateRewards($latestScore->score, $gameId, $userId);
            
            // Calculate XP earned (example: 1 XP per 10 points)
            $xpEarned = floor($latestScore->score / 10);
            
            // Get game details
            $game = Game::find($gameId);
            
            // Parse game stats
            $gameStats = json_decode($latestScore->game_stats, true) ?? [];
            
            // Calculate coins earned (example: 1 coin per 100 points)
            $coinsEarned = floor($latestScore->score / 100);
            
            // Get accuracy
            $accuracy = $this->calculateAccuracy($latestScore);
            
            return response()->json([
                'success' => true,
                'summary' => [
                    'score' => $latestScore->score,
                    'time_taken' => $latestScore->time_taken,
                    'rank' => $rank,
                    'total_players' => $totalPlayers,
                    'accuracy' => $accuracy,
                    'rewards' => $rewards,
                    'xp_earned' => $xpEarned,
                    'coins_earned' => $coinsEarned,
                    'game_title' => $game ? $game->title : 'Game ' . $gameId,
                    'game_id' => $gameId,
                    'user_id' => $userId,
                    'game_stats' => $gameStats,
                    'played_at' => $latestScore->created_at->format('Y-m-d H:i:s'),
                    'score_id' => $latestScore->id
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting game summary', [
                'game_id' => $gameId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving game summary',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get leaderboard for a specific game
     */
    public function getLeaderboard($gameId)
    {
        try {
            // Get current user ID for highlighting
            $currentUserId = null;
            if (Auth::check()) {
                $currentUserId = Auth::id();
            } else {
                $currentUserId = request()->input('user_id');
            }
            
            \Log::info('Getting leaderboard', [
                'game_id' => $gameId,
                'current_user_id' => $currentUserId
            ]);
            
            // Get top scores for this game (best score per user)
            $leaderboard = DB::table('game_scores')
                ->select([
                    'user_id',
                    DB::raw('MAX(score) as best_score'),
                    DB::raw('MIN(time_taken) as best_time'),
                    DB::raw('MAX(created_at) as last_played')
                ])
                ->where('game_id', $gameId)
                ->groupBy('user_id')
                ->orderBy('best_score', 'DESC')
                ->orderBy('best_time', 'ASC')
                ->take(20)
                ->get();
            
            // Format leaderboard with user names
            $formattedLeaderboard = [];
            $rank = 1;
            
            foreach ($leaderboard as $entry) {
                // Try to get user info
                $user = DB::table('users')->where('id', $entry->user_id)->first();
                
                $formattedLeaderboard[] = [
                    'rank' => $rank,
                    'user_id' => $entry->user_id,
                    'user_name' => $user ? $user->name : 'Player ' . substr($entry->user_id, 0, 6),
                    'user_avatar' => $user ? ($user->avatar ?? null) : null,
                    'score' => $entry->best_score,
                    'time_taken' => $entry->best_time,
                    'last_played' => $entry->last_played,
                    'is_current_user' => $currentUserId && $entry->user_id == $currentUserId
                ];
                
                $rank++;
            }
            
            // Get current user's position if not in top 20
            $userPosition = null;
            $userBestScore = null;
            $userBestTime = null;
            
            if ($currentUserId) {
                $userBest = DB::table('game_scores')
                    ->select([
                        DB::raw('MAX(score) as best_score'),
                        DB::raw('MIN(time_taken) as best_time')
                    ])
                    ->where('game_id', $gameId)
                    ->where('user_id', $currentUserId)
                    ->first();
                
                if ($userBest && $userBest->best_score) {
                    $userBestScore = $userBest->best_score;
                    $userBestTime = $userBest->best_time;
                    
                    // Calculate user's global rank
                    $usersWithHigherScore = DB::table('game_scores')
                        ->select('user_id')
                        ->where('game_id', $gameId)
                        ->where('score', '>', $userBestScore)
                        ->groupBy('user_id')
                        ->get()
                        ->count();
                    
                    $userPosition = $usersWithHigherScore + 1;
                }
            }
            
            // Get total unique players
            $totalPlayers = DB::table('game_scores')
                ->where('game_id', $gameId)
                ->distinct('user_id')
                ->count();
            
            // Get game info
            $game = Game::find($gameId);
            
            return response()->json([
                'success' => true,
                'leaderboard' => $formattedLeaderboard,
                'user_position' => $userPosition,
                'user_best_score' => $userBestScore,
                'user_best_time' => $userBestTime,
                'total_players' => $totalPlayers,
                'game_id' => $gameId,
                'game_title' => $game ? $game->title : 'Game ' . $gameId
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting leaderboard', [
                'game_id' => $gameId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving leaderboard',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Collect rewards after game completion
     */
    public function collectRewards(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $validated = $request->validate([
                'game_id' => 'required|integer',
                'score' => 'required|integer',
                'score_id' => 'required|integer',
                'rewards' => 'nullable|array'
            ]);
            
            \Log::info('Collecting rewards', [
                'user_id' => $user->id,
                'game_id' => $validated['game_id'],
                'score' => $validated['score']
            ]);
            
            // Get the score record
            $scoreRecord = GameScore::find($validated['score_id']);
            
            if (!$scoreRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Score record not found'
                ], 404);
            }
            
            // Mark rewards as collected
            $scoreRecord->update([
                'rewards_collected' => true,
                'rewards_collected_at' => now()
            ]);
            
            // Calculate rewards
            $rewards = $this->calculateRewards($validated['score'], $validated['game_id'], $user->id);
            
            // Calculate XP and coins
            $xpEarned = floor($validated['score'] / 10);
            $coinsEarned = floor($validated['score'] / 100);
            
            // Here you would typically update user's XP and coins in the database
            // For now, just return the calculated values
            
            return response()->json([
                'success' => true,
                'message' => 'Rewards collected successfully!',
                'xp_earned' => $xpEarned,
                'coins_earned' => $coinsEarned,
                'rewards' => $rewards,
                'total_rewards' => count($rewards)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error collecting rewards', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error collecting rewards',
                'error' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Calculate rewards based on score
     */
    private function calculateRewards($score, $gameId, $userId)
    {
        $rewards = [];
        
        // Base XP reward
        $baseXP = floor($score / 10);
        if ($baseXP > 0) {
            $rewards[] = [
                'type' => 'xp',
                'name' => 'Experience Points',
                'description' => 'Dasar pengalaman bermain',
                'amount' => $baseXP,
                'icon' => '⭐'
            ];
        }
        
        // Coin reward
        $coins = floor($score / 100);
        if ($coins > 0) {
            $rewards[] = [
                'type' => 'coins',
                'name' => 'Koin',
                'description' => 'Mata wang dalam permainan',
                'amount' => $coins,
                'icon' => '🪙'
            ];
        }
        
        // Score-based achievements
        if ($score >= 1000) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Master Pemain',
                'description' => 'Mencapai 1000 mata',
                'badge' => 'gold',
                'icon' => '🏆'
            ];
        }
        
        if ($score >= 800) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Pemain Mahir',
                'description' => 'Mencapai 800 mata',
                'badge' => 'silver',
                'icon' => '⭐'
            ];
        }
        
        if ($score >= 500) {
            $rewards[] = [
                'type' => 'achievement', 
                'name' => 'Pemain Cemerlang',
                'description' => 'Mencapai 500 mata',
                'badge' => 'bronze',
                'icon' => '🎯'
            ];
        }
        
        // Check play count
        $playCount = GameScore::where('game_id', $gameId)
            ->where('user_id', $userId)
            ->count();
        
        if ($playCount == 1) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Pemain Baharu',
                'description' => 'Kali pertama bermain permainan ini',
                'badge' => 'new',
                'icon' => '🎮'
            ];
        }
        
        // Perfect score reward
        $game = Game::find($gameId);
        if ($game && $score >= 1000) {
            $rewards[] = [
                'type' => 'achievement',
                'name' => 'Skor Sempurna',
                'description' => 'Mendapat skor maksimum',
                'badge' => 'perfect',
                'icon' => '💯'
            ];
        }
        
        return $rewards;
    }

    /**
     * Calculate accuracy percentage
     */
    private function calculateAccuracy($score)
    {
        if (!$score) {
            return 0;
        }
        
        // Parse game stats
        $stats = json_decode($score->game_stats, true) ?? [];
        
        // Calculate accuracy from stats
        if (isset($stats['correct'], $stats['total']) && $stats['total'] > 0) {
            return round(($stats['correct'] / $stats['total']) * 100);
        }
        
        if (isset($stats['accuracy'])) {
            return $stats['accuracy'];
        }
        
        // Default calculation based on score (for demo purposes)
        if ($score >= 900) return 95;
        if ($score >= 700) return 85;
        if ($score >= 500) return 75;
        if ($score >= 300) return 60;
        return 50;
    }

    // ========== GAME MANAGEMENT METHODS (for editing) ==========

    /**
     * Show the form for editing the specified game.
     */
    public function edit($id)
    {
        $game = Game::with('creator')->findOrFail($id);
        
        return response()->json([
            'game' => $game,
            'editable_fields' => [
                'title', 'description', 'difficulty', 'category',
                'time_limit', 'points_per_question', 'cover_image',
                'game_file', 'additional_files', 'game_settings'
            ]
        ]);
    }

    /**
     * Update the specified game in storage.
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        // Validate the update request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255|unique:games,title,' . $id,
            'description' => 'sometimes|string',
            'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
            'category' => 'sometimes|string|max:100',
            'time_limit' => 'sometimes|nullable|integer|min:1',
            'points_per_question' => 'sometimes|integer|min:1',
            'cover_image' => 'sometimes|file|image|mimes:jpeg,png,jpg,gif|max:5120',
            'game_file' => 'sometimes|file|mimes:zip,html,js,css,json,php|max:20480',
            'additional_files.*' => 'sometimes|file|max:5120',
            'notify_students' => 'sometimes|boolean',
            'game_settings' => 'sometimes|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $updateData = $request->only([
                'title', 'description', 'difficulty', 'category',
                'time_limit', 'points_per_question', 'game_settings'
            ]);

            // Handle cover image update
            if ($request->hasFile('cover_image')) {
                if ($game->cover_image && Storage::exists($game->cover_image)) {
                    Storage::delete($game->cover_image);
                }
                
                $coverPath = $request->file('cover_image')->store('games/covers', 'public');
                $updateData['cover_image'] = $coverPath;
            }

            // Handle main game file update
            $gameFileUpdated = false;
            if ($request->hasFile('game_file')) {
                if ($game->game_file && Storage::exists($game->game_file)) {
                    Storage::delete($game->game_file);
                }
                
                $gameFilePath = $request->file('game_file')->store('games/files', 'public');
                $updateData['game_file'] = $gameFilePath;
                $updateData['version'] = $game->version + 1;
                $gameFileUpdated = true;
            }

            // Handle additional files update
            if ($request->hasFile('additional_files')) {
                $additionalFiles = [];
                
                if ($game->additional_files) {
                    foreach ($game->additional_files as $oldFile) {
                        if (Storage::exists($oldFile)) {
                            Storage::delete($oldFile);
                        }
                    }
                }

                foreach ($request->file('additional_files') as $file) {
                    $filePath = $file->store('games/additional', 'public');
                    $additionalFiles[] = $filePath;
                }
                
                $updateData['additional_files'] = $additionalFiles;
            }

            $updateData['last_updated_at'] = now();
            $game->update($updateData);

            // Notify students if requested
            $studentsNotified = false;
            if ($request->boolean('notify_students') && $gameFileUpdated) {
                $studentsNotified = $this->notifyStudentsAboutUpdate($game);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Game updated successfully',
                'game' => $game->fresh('creator'),
                'version_incremented' => $gameFileUpdated,
                'students_notified' => $studentsNotified,
                'changes' => $game->getChanges()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update game',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Partial update for specific fields (PATCH)
     */
    public function partialUpdate(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255|unique:games,title,' . $id,
            'description' => 'sometimes|string',
            'difficulty' => ['sometimes', Rule::in(['easy', 'medium', 'hard'])],
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $game->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Game updated successfully',
            'game' => $game->fresh('creator')
        ]);
    }

    /**
     * Get game update history
     */
    public function getUpdateHistory($id)
    {
        $game = Game::findOrFail($id);
        
        return response()->json([
            'game_id' => $game->id,
            'title' => $game->title,
            'current_version' => $game->version,
            'last_updated' => $game->last_updated_at,
            'created_at' => $game->created_at,
            'total_updates' => $game->version - 1,
            'has_major_changes' => $game->version > 1
        ]);
    }

    /**
     * Validate game data before update
     */
    public function validateGameData(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:games,title,' . $id,
            'description' => 'required|string',
            'difficulty' => ['required', Rule::in(['easy', 'medium', 'hard'])],
            'category' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Game data is valid'
        ]);
    }

    /**
     * Notify students about game updates
     */
    private function notifyStudentsAboutUpdate(Game $game): bool
    {
        try {
            $students = DB::table('user_game_progress')
                ->where('game_id', $game->id)
                ->join('users', 'user_game_progress.user_id', '=', 'users.id')
                ->select('users.id', 'users.email', 'users.name')
                ->get();

            $notifications = [];
            foreach ($students as $student) {
                $notifications[] = [
                    'user_id' => $student->id,
                    'type' => 'game_updated',
                    'title' => 'Game Updated: ' . $game->title,
                    'message' => 'The game "' . $game->title . '" has been updated to version ' . $game->version . '.',
                    'data' => json_encode([
                        'game_id' => $game->id,
                        'game_title' => $game->title,
                        'version' => $game->version,
                        'updated_at' => $game->last_updated_at
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($notifications)) {
                DB::table('notifications')->insert($notifications);
            }

            \Log::info("Game update notification sent for '{$game->title}' to {$students->count()} students");
            return true;

        } catch (\Exception $e) {
            \Log::error("Failed to notify students about game update: " . $e->getMessage());
            return false;
        }
    }
}