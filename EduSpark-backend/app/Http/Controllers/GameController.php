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
            'game_file' => 'sometimes|file|mimes:zip,html,js,css,json,php|max:20480', // 20MB max
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
            // Start transaction for atomic update
            DB::beginTransaction();

            $updateData = $request->only([
                'title', 'description', 'difficulty', 'category',
                'time_limit', 'points_per_question', 'game_settings'
            ]);

            // Handle cover image update
            if ($request->hasFile('cover_image')) {
                // Delete old cover image if exists
                if ($game->cover_image && Storage::exists($game->cover_image)) {
                    Storage::delete($game->cover_image);
                }
                
                $coverPath = $request->file('cover_image')->store('games/covers', 'public');
                $updateData['cover_image'] = $coverPath;
            }

            // Handle main game file update (triggers version increment)
            $gameFileUpdated = false;
            if ($request->hasFile('game_file')) {
                // Delete old game file if exists
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
                
                // Delete old additional files if they exist
                if ($game->additional_files) {
                    foreach ($game->additional_files as $oldFile) {
                        if (Storage::exists($oldFile)) {
                            Storage::delete($oldFile);
                        }
                    }
                }

                // Store new additional files
                foreach ($request->file('additional_files') as $file) {
                    $filePath = $file->store('games/additional', 'public');
                    $additionalFiles[] = $filePath;
                }
                
                $updateData['additional_files'] = $additionalFiles;
            }

            // Update timestamps
            $updateData['last_updated_at'] = now();

            // Perform the update
            $game->update($updateData);

            // Notify students if requested and game file was updated
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
            // Get students who have progress in this game
            $students = DB::table('user_game_progress')
                ->where('game_id', $game->id)
                ->join('users', 'user_game_progress.user_id', '=', 'users.id')
                ->select('users.id', 'users.email', 'users.name')
                ->get();

            // Create notification records
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

            // Insert notifications (you might have a notifications table)
            if (!empty($notifications)) {
                DB::table('notifications')->insert($notifications);
            }

            // Log the notification
            \Log::info("Game update notification sent for '{$game->title}' to {$students->count()} students");

            return true;

        } catch (\Exception $e) {
            \Log::error("Failed to notify students about game update: " . $e->getMessage());
            return false;
        }
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
        
        return response()->json([
            'success' => true,
            'message' => 'Score saved successfully',
            'score_id' => $gameScore->id
        ]);
    }

    // ========== NEW METHODS ADDED FOR GAME SUMMARY & LEADERBOARD ==========

    /**
     * Get game summary after completion
     */
    public function getGameSummary($gameId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        $latestScore = GameScore::where('user_id', $user->id)
            ->where('game_id', $gameId)
            ->latest()
            ->first();
        
        if (!$latestScore) {
            return response()->json([
                'success' => false,
                'message' => 'No game data found'
            ]);
        }
        
        // Calculate rank
        $rank = GameScore::where('game_id', $gameId)
            ->where('score', '>', $latestScore->score)
            ->count() + 1;
        
        // Calculate rewards
        $rewards = $this->calculateRewards($latestScore->score, $gameId, $user->id);
        
        // Get game details
        $game = Game::find($gameId);
        
        return response()->json([
            'success' => true,
            'summary' => [
                'score' => $latestScore->score,
                'time_taken' => $latestScore->time_taken,
                'rank' => $rank,
                'total_players' => GameScore::where('game_id', $gameId)->distinct('user_id')->count(),
                'accuracy' => $this->calculateAccuracy($latestScore),
                'rewards' => $rewards,
                'game_title' => $game ? $game->title : 'Unknown Game',
                'game_id' => $gameId,
                'user_name' => $user->name
            ]
        ]);
    }

    /**
     * Get leaderboard for a specific game
     */
    public function getLeaderboard($gameId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get top 10 scores for this game
        $leaderboard = GameScore::with('user')
            ->where('game_id', $gameId)
            ->orderBy('score', 'DESC')
            ->orderBy('time_taken', 'ASC')
            ->take(10)
            ->get()
            ->map(function ($score, $index) use ($user) {
                return [
                    'rank' => $index + 1,
                    'user_name' => $score->user ? $score->user->name : 'Unknown User',
                    'score' => $score->score,
                    'time_taken' => $score->time_taken,
                    'created_at' => $score->created_at,
                    'is_current_user' => $score->user_id == $user->id
                ];
            });
        
        // Get current user's rank if not in top 10
        $userScore = GameScore::where('game_id', $gameId)
            ->where('user_id', $user->id)
            ->latest()
            ->first();
        
        $userRank = null;
        if ($userScore) {
            $userRank = GameScore::where('game_id', $gameId)
                ->where('score', '>', $userScore->score)
                ->count() + 1;
        }
        
        return response()->json([
            'success' => true,
            'leaderboard' => $leaderboard,
            'user_rank' => $userRank,
            'user_score' => $userScore ? $userScore->score : 0,
            'user_time' => $userScore ? $userScore->time_taken : 0,
            'total_players' => GameScore::where('game_id', $gameId)->distinct('user_id')->count()
        ]);
    }

    /**
     * Collect rewards after game completion
     */
    public function collectRewards(Request $request)
    {
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
        ]);
        
        // Here you would typically:
        // 1. Update user's XP/points
        // 2. Add rewards to user's inventory
        // 3. Log the reward collection
        
        // For now, just return success
        return response()->json([
            'success' => true,
            'message' => 'Rewards collected successfully!',
            'xp_earned' => $validated['score'] / 10, // Example: 10 points = 1 XP
            'rewards' => $this->calculateRewards($validated['score'], $validated['game_id'], $user->id)
        ]);
    }

    /**
     * Calculate rewards based on score
     */
    private function calculateRewards($score, $gameId, $userId)
    {
        $rewards = [];
        
        // Example reward logic
        if ($score >= 1000) {
            $rewards[] = [
                'name' => 'Master Pemain',
                'description' => 'Mencapai 1000 mata',
                'xp' => 100,
                'icon' => '🏆'
            ];
        }
        
        if ($score >= 500) {
            $rewards[] = [
                'name' => 'Pemain Mahir', 
                'description' => 'Mencapai 500 mata',
                'xp' => 50,
                'icon' => '⭐'
            ];
        }
        
        // First time playing reward
        $playCount = GameScore::where('game_id', $gameId)
            ->where('user_id', $userId)
            ->count();
        
        if ($playCount == 1) {
            $rewards[] = [
                'name' => 'Pemain Baharu',
                'description' => 'Kali pertama bermain',
                'xp' => 25,
                'icon' => '🎯'
            ];
        }
        
        // Speed reward (if time taken < 60 seconds)
        $latestScore = GameScore::where('game_id', $gameId)
            ->where('user_id', $userId)
            ->latest()
            ->first();
        
        if ($latestScore && $latestScore->time_taken < 60) {
            $rewards[] = [
                'name' => 'Pantas Tangkas',
                'description' => 'Selesai dalam masa 60 saat',
                'xp' => 30,
                'icon' => '⚡'
            ];
        }
        
        // Perfect score reward
        if ($score >= 1000) {
            $rewards[] = [
                'name' => 'Skor Sempurna',
                'description' => 'Mendapat skor maksimum',
                'xp' => 75,
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
        // Example: Calculate based on game stats
        $stats = json_decode($score->game_stats, true) ?? [];
        
        if (isset($stats['correct'], $stats['total']) && $stats['total'] > 0) {
            return round(($stats['correct'] / $stats['total']) * 100);
        }
        
        if (isset($stats['accuracy'])) {
            return $stats['accuracy'];
        }
        
        return 85; // Default if no stats
    }

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
}