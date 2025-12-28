<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameScore;
use Illuminate\Http\Request;

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
            // Teacher view - show all their games with management options
            $games = Game::where('teacher_id', $user->id)->get();
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
        
        // Verify teacher owns this game
        if ($game->teacher_id !== auth()->id()) {
            abort(403);
        }

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
     * Delete game
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        
        // Verify teacher owns this game
        if ($game->teacher_id !== auth()->id()) {
            abort(403);
        }

        $game->delete();

        return back()->with('success', 'Game deleted successfully!');
    }

    /**
     * Get leaderboard for a game (for teachers to see class performance)
     */
    public function leaderboard($id)
    {
        $game = Game::findOrFail($id);
        $user = auth()->user();
        $scores = collect(); // Initialize empty collection

        // Only teacher who created the game can view class leaderboard
        // Students can view public leaderboard
        if ($user && $user->role === 'teacher' && $game->teacher_id === $user->id) {
            // Teacher view - all scores for this game
            $scores = GameScore::where('game_id', $id)
                ->with('user')
                ->orderBy('score', 'desc')
                ->orderBy('time_taken', 'asc')
                ->get();
        } else {
            // Student view - all scores (could add privacy later)
            $scores = GameScore::where('game_id', $id)
                ->with('user')
                ->orderBy('score', 'desc')
                ->orderBy('time_taken', 'asc')
                ->get();
        }
        
        return view('games.leaderboard', compact('game', 'scores'));
    }
}
