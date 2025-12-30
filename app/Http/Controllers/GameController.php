<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use App\Models\Leaderboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class GameController extends Controller
{
    /**
     * Display games page (student view shows games to play, teacher view shows management)
     */
    public function index()
    {
        $user = auth()->user();
        $games = collect(); // Initialize empty collection
        
        if ($user && $user->role === 'teacher') {
            // Teacher view - show ALL games (all teachers can manage all games)
            $games = Game::all();
        } else {
            // Student view - show published games
            $games = Game::where('is_published', true)->get();
        }
        
        return view('games.index', compact('games'));
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
            Leaderboard::create([
                'user_id' => $user->id,
                'username' => $user->name,
                'class' => $user->meta['class'] ?? 'Unknown', // Assuming class is in user meta
                'game_id' => $game->slug,
                'score' => $validated['score'],
                'time_taken' => $validated['time_taken'],
                'timestamp' => now(),
            ]);
        }

        // Store result in session for the result view
        session([
            'game_result' => [
                'game_id' => $id,
                'game_slug' => $game->slug,
                'game_title' => $game->title,
                'score' => $validated['score'],
                'time_taken' => $validated['time_taken'],
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

        return view('games.result', compact('game', 'result'));
    }

    /**
     * Get leaderboard for a game
     */
    public function leaderboard($id)
    {
        $game = Game::findOrFail($id);
        $scores = collect();
        $currentUser = auth()->user();
        $highlightedUserIndex = -1;
        
        // First, try to get from existing leaderboard table if it exists
        if (Schema::hasTable('leaderboard')) {
            $leaderboardEntries = Leaderboard::where('game_id', $game->slug)
                ->orderBy('score', 'desc')
                ->orderBy('timestamp', 'desc')
                ->get();
            
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
        
        return view('games.leaderboard', compact('game', 'scores', 'currentUser', 'highlightedUserIndex'));
    }
}
