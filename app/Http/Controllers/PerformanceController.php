<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index()
    {
        $studentId = 2; // temporary test user (replace with Auth::id() later)

        // --- QUIZ SECTION ---
        $avgQuizScore = DB::table('quiz_attempt')
            ->where('user_id', $studentId)
            ->avg('score');

        $totalQuizzes = DB::table('quiz_attempt')
            ->where('user_id', $studentId)
            ->count();

        $weakTopic = DB::table('quiz_attempt as a')
            ->join('quiz as q', 'a.quiz_id', '=', 'q.id')
            ->where('a.user_id', $studentId)
            ->select('q.title', DB::raw('AVG(a.score) as avg_score'))
            ->groupBy('q.title')
            ->orderBy('avg_score', 'asc')
            ->limit(1)
            ->value('q.title');

        // --- GAME SECTION ---
        $avgGameScore = DB::table('game_score')
            ->where('user_id', $studentId)
            ->avg('score');

        $totalGames = DB::table('game_score')
            ->where('user_id', $studentId)
            ->count();

        $bestGame = DB::table('game_score as s')
            ->join('game as g', 's.game_id', '=', 'g.id')
            ->where('s.user_id', $studentId)
            ->orderBy('s.score', 'desc')
            ->limit(1)
            ->value('g.name');

        // --- RECENT ACTIVITY ---
        $recentQuizzes = DB::table('quiz_attempt as a')
            ->join('quiz as q', 'a.quiz_id', '=', 'q.id')
            ->where('a.user_id', $studentId)
            ->orderBy('a.completed_at', 'desc')
            ->limit(6)
            ->select('q.title', 'a.score', 'a.completed_at')
            ->get();

        $recentGames = DB::table('game_score as s')
            ->join('game as g', 's.game_id', '=', 'g.id')
            ->where('s.user_id', $studentId)
            ->orderBy('s.created_at', 'desc')
            ->limit(6)
            ->select('g.name as title', 's.score', 's.created_at as completed_at')
            ->get();

        // Combine recent quiz & game attempts
        $recentData = $recentQuizzes->concat($recentGames)->sortByDesc('completed_at')->take(6);

        // Prepare data for chart
        $labels = $recentData->pluck('title')->values();
        $scores = $recentData->pluck('score')->values();

        return view('performance.index', [
            'avgQuizScore' => round($avgQuizScore ?? 0, 1),
            'avgGameScore' => round($avgGameScore ?? 0, 1),
            'totalQuizzes' => $totalQuizzes,
            'totalGames' => $totalGames,
            'weakTopic' => $weakTopic ?? 'N/A',
            'bestGame' => $bestGame ?? 'N/A',
            'labels' => $labels,
            'scores' => $scores,
        ]);
    }
}
