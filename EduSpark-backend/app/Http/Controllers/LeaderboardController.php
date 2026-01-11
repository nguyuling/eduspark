<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaderboardController extends Controller
{
    /**
     * Display leaderboard rankings.
     *
     * GET /api/leaderboard
     * Query params: game_id, class, period (all|today|week|month)
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_id' => 'nullable|string|max:50',
            'class' => 'nullable|string|max:20',
            'period' => 'nullable|in:all,today,week,month',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid parameters',
                'details' => $validator->errors()
            ], 400);
        }

        $gameId = $request->query('game_id');
        $class = $request->query('class');
        $period = $request->query('period', 'all');

        // Build base query
        $query = DB::table('leaderboard')
            ->select(
                'user_id',
                'username',
                'class',
                'game_id',
                'score',
                'time_taken',
                'timestamp',
                DB::raw('RANK() OVER (ORDER BY score DESC, timestamp ASC) as rank')
            );

        // Apply filters
        if ($gameId) {
            $query->where('game_id', $gameId);
        }
        if ($class) {
            $query->where('class', $class);
        }

        // Time filtering
        switch ($period) {
            case 'today':
                $query->whereDate('timestamp', today());
                break;
            case 'week':
                $query->whereBetween('timestamp', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('timestamp', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
        }

        $entries = $query->orderBy('score', 'desc')
                         ->orderBy('timestamp', 'asc')
                         ->get();

        return response()->json($entries);
    }

    /**
     * Store a new score.
     *
     * POST /api/leaderboard
     * Body: { user_id, username, class, game_id, score, time_taken }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'username' => 'required|string|max:100',
            'class' => 'required|string|max:20',
            'game_id' => 'required|string|max:50',
            'score' => 'required|integer|min:0',
            'time_taken' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->only(['user_id', 'username', 'class', 'game_id', 'score', 'time_taken']);
            $data['timestamp'] = now();

            DB::table('leaderboard')->insert($data);

            return response()->json([
                'message' => 'Score saved successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Database error',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset leaderboard (Teacher only).
     *
     * DELETE /api/leaderboard
     * Query: game_id, class (optional)
     */
    public function reset(Request $request)
    {
        // 🔐 Add teacher auth later: $request->user()->role === 'teacher'
        
        $gameId = $request->query('game_id');
        $class = $request->query('class');

        $query = DB::table('leaderboard');

        if ($gameId) {
            $query->where('game_id', $gameId);
        }
        if ($class) {
            $query->where('class', $class);
        }

        $count = $query->delete();

        return response()->json([
            'message' => "$count record(s) deleted",
            'filters' => [
                'game_id' => $gameId,
                'class' => $class
            ]
        ]);
    }
}