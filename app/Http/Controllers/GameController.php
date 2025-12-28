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
     * Get leaderboard for a game
     */
    public function leaderboard($id)
    {
        $game = Game::findOrFail($id);
        $scores = collect();
        
        // First, try to get from existing leaderboard table if it exists
        if (Schema::hasTable('leaderboard')) {
            $leaderboardEntries = Leaderboard::where('game_id', $game->slug)
                ->orderBy('score', 'desc')
                ->orderBy('timestamp', 'asc')
                ->get();
            
            if ($leaderboardEntries->isNotEmpty()) {
                // Transform leaderboard data to match our view structure
                foreach ($leaderboardEntries as $entry) {
                    $scores->push((object)[
                        'user_id' => $entry->user_id,
                        'score' => $entry->score,
                        'time_taken' => null,
                        'completed_at' => $entry->timestamp,
                        'user' => (object)[
                            'name' => $entry->username,
                            'email' => $entry->class,
                        ],
                    ]);
                }
            }
        }
        
        // Fallback to GameScore table if no leaderboard entries found
        if ($scores->isEmpty()) {
            $scores = GameScore::where('game_id', $id)
                ->with('user')
                ->orderBy('score', 'desc')
                ->orderBy('time_taken', 'asc')
                ->get();
        }
        
        return view('games.leaderboard', compact('game', 'scores'));
    }
}
