<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameTeacherController extends Controller
{
    /**
     * Display teacher game management dashboard
     */
    public function index()
    {
        $games = Game::where('teacher_id', auth()->id())->get();
        return view('games.teacher.index', compact('games'));
    }

    /**
     * Show form to create new game
     */
    public function create()
    {
        return view('games.teacher.create');
    }

    /**
     * Store new game
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
            'slug' => 'nullable|string|max:255',
            'game_type' => 'nullable|string|max:100',
            'topic' => 'nullable|string|max:100',
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        if (!isset($validated['slug'])) {
            $validated['slug'] = str_slug($validated['title']);
        }

        Game::create($validated);

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game created successfully!');
    }

    /**
     * Show teacher game edit form
     */
    public function edit($id)
    {
        $game = Game::findOrFail($id);
        return view('games.teacher.edit', compact('game'));
    }

    /**
     * Update game
     */
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:games,title,' . $id,
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
            'slug' => 'nullable|string|max:255',
            'game_type' => 'nullable|string|max:100',
            'topic' => 'nullable|string|max:100',
        ]);

        $validated['is_published'] = $request->has('is_published');
        
        if (!isset($validated['slug'])) {
            $validated['slug'] = str_slug($validated['title']);
        }

        $game->update($validated);

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Delete game (soft delete)
     */
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return back()
            ->with('success', 'Game deleted successfully! You can undo this action.');
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
