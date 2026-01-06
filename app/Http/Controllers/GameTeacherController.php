<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameTeacherController extends Controller
{
    /**
     * Display teacher game management dashboard
     */
    public function index()
    {
        $games = Game::where('teacher_id', auth()->id())
            ->whereNull('deleted_at')
            ->get();

        if (request()->wantsJson()) {
            return response()->json($games);
        }

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
            'game_file' => 'nullable|file|mimes:zip,html,js,php|max:10240', // 10MB max
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle file upload
        if ($request->hasFile('game_file')) {
            $file = $request->file('game_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('games', $filename, 'public');
            $validated['game_file'] = $path;
        }

        Game::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Game created successfully!',
            ], 201);
        }

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
            'game_file' => 'nullable|file|mimes:zip,html,js,php|max:10240', // 10MB max
        ]);

        $validated['is_published'] = $request->has('is_published');
        
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle file upload
        if ($request->hasFile('game_file')) {
            // Delete old file if exists
            if ($game->game_file && \Storage::disk('public')->exists($game->game_file)) {
                \Storage::disk('public')->delete($game->game_file);
            }
            
            $file = $request->file('game_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('games', $filename, 'public');
            $validated['game_file'] = $path;
        }

        $game->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Game updated successfully!',
            ]);
        }

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Delete game (soft delete)
     */
    public function destroy($id)
    {
        $game = Game::where('teacher_id', auth()->id())->findOrFail($id);
        $gameTitle = $game->title;
        $game->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => "Game '{$gameTitle}' deleted successfully!",
                'undo_game_id' => $id,
            ]);
        }

        return redirect()->route('teacher.games.index')
            ->with('success', "Game '{$gameTitle}' deleted successfully!")
            ->with('undo_available', true)
            ->with('undo_game_id', $id);
    }

    /**
     * Restore soft-deleted game
     */
    public function restore($id)
    {
        $game = Game::withTrashed()->where('teacher_id', auth()->id())->findOrFail($id);
        $gameTitle = $game->title;
        $game->restore();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => "Game '{$gameTitle}' restored successfully!",
            ]);
        }

        return redirect()->route('teacher.games.index')
            ->with('success', "Game '{$gameTitle}' restored successfully!");
    }
}
