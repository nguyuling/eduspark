<?php
namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameTeacherController extends Controller
{
    /**
     * Display teacher game management dashboard
     */
    public function index()
    {
        $games = Game::where('teacher_id', auth()->id())->get();
        return view('teacher.games.index', compact('games'));
    }

    /**
     * Show form to create new game
     */
    public function create()
    {
        return view('teacher.games.create');
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
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        Game::create($validated);

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game created successfully!');
    }

    /**
     * Show teacher game edit form
     */
    public function edit($id)
    {
        $game = Game::where('teacher_id', auth()->id())->findOrFail($id);
        return view('teacher.games.edit', compact('game'));
    }

    /**
     * Update game
     */
    public function update(Request $request, $id)
    {
        $game = Game::where('teacher_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:games,title,' . $id,
            'description' => 'nullable|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'required|string|max:100',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $game->update($validated);

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game updated successfully!');
    }

    /**
     * Delete game
     */
    public function destroy($id)
    {
        $game = Game::where('teacher_id', auth()->id())->findOrFail($id);
        $game->delete();

        return redirect()->route('teacher.games.index')
            ->with('success', 'Game deleted successfully!');
    }
}