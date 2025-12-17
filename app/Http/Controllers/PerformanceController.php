<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class PerformanceController extends Controller
{
    public function index()
    {
        // use the authenticated user when available, otherwise test id 2
        $studentId = Auth::id() ?? 2;

        // Defaults (safe values so the view won't break)
        $avgQuizScore = 0;
        $totalQuizzes = 0;
        $weakTopic = 'N/A';
        $avgGameScore = 0;
        $totalGames = 0;
        $bestGame = 'N/A';
        $labels = [];
        $scores = [];

        $recentCollection = collect();

        //
        // --- QUIZ SECTION ---
        //
        if (Schema::hasTable('quiz_attempts') || Schema::hasTable('quiz_attempt')) {
            $quizTable = Schema::hasTable('quiz_attempts') ? 'quiz_attempts' : 'quiz_attempt';
            $quizUserCol = Schema::hasColumn($quizTable, 'student_id') ? 'student_id' : (Schema::hasColumn($quizTable, 'user_id') ? 'user_id' : 'user_id');

            // Calculate average as percentage: join to get total points per quiz
            $avgPercentage = DB::table($quizTable . ' as a')
                ->join('quizzes as q', 'a.quiz_id', '=', 'q.id')
                ->join('questions as qn', 'q.id', '=', 'qn.quiz_id')
                ->where("a.$quizUserCol", $studentId)
                ->whereNotNull('a.score')
                ->whereNotNull('a.submitted_at')
                ->select(
                    'a.id',
                    DB::raw('a.score as earned'),
                    DB::raw('SUM(qn.points) as total')
                )
                ->groupBy('a.id', 'a.score')
                ->get()
                ->map(function($row) {
                    return $row->total > 0 ? ($row->earned / $row->total) * 100 : 0;
                })
                ->avg();

            $avgQuizScore = round($avgPercentage ?? 0, 1);

            $totalQuizzes = DB::table($quizTable)
                ->where($quizUserCol, $studentId)
                ->whereNotNull('score')
                ->whereNotNull('submitted_at')
                ->count();

            // weakest topic: calculate as percentage
            $quizMetaTable = Schema::hasTable('quizzes') ? 'quizzes' : (Schema::hasTable('quiz') ? 'quiz' : null);

            if ($quizMetaTable) {
                $topicPercentages = DB::table($quizTable . ' as a')
                    ->join($quizMetaTable . ' as q', 'a.quiz_id', '=', 'q.id')
                    ->join('questions as qn', 'q.id', '=', 'qn.quiz_id')
                    ->where("a.$quizUserCol", $studentId)
                    ->whereNotNull('a.score')
                    ->whereNotNull('a.submitted_at')
                    ->select(
                        'q.id',
                        'q.title',
                        'a.score as earned',
                        DB::raw('SUM(qn.points) as total')
                    )
                    ->groupBy('q.id', 'q.title', 'a.id', 'a.score')
                    ->get()
                    ->groupBy('title')
                    ->map(function($attempts) {
                        return $attempts->map(function($row) {
                            return $row->total > 0 ? ($row->earned / $row->total) * 100 : 0;
                        })->avg();
                    })
                    ->sortBy(function($percentage) {
                        return $percentage;
                    });

                $weakTopic = $topicPercentages->keys()->first() ?? 'N/A';
            }

            // Determine a timestamp column to use for ordering/display (safe fallback)
            $quizTimestampCol = null;
            foreach (['completed_at', 'created_at', 'updated_at'] as $col) {
                if (Schema::hasColumn($quizTable, $col)) {
                    $quizTimestampCol = $col;
                    break;
                }
            }

            // Fetch recent quiz rows with percentage calculation
            $recentQuizRows = DB::table($quizTable . ' as a')
                ->join('quizzes as q', 'a.quiz_id', '=', 'q.id')
                ->join('questions as qn', 'q.id', '=', 'qn.quiz_id')
                ->where("a.$quizUserCol", $studentId)
                ->whereNotNull('a.score')
                ->whereNotNull('a.submitted_at')
                ->select(
                    'a.id',
                    'q.title',
                    'a.score as earned',
                    'a.submitted_at as completed_at',
                    DB::raw('SUM(qn.points) as total')
                )
                ->groupBy('a.id', 'q.title', 'a.score', 'a.submitted_at')
                ->orderBy('a.submitted_at', 'desc')
                ->limit(6)
                ->get();

            // normalize and push into recentCollection with percentage scores
            foreach ($recentQuizRows as $r) {
                $percentage = $r->total > 0 ? round(($r->earned / $r->total) * 100, 1) : 0;
                $recentCollection->push((object)[
                    'title' => $r->title ?? ('Kuiz #' . $r->id),
                    'score' => $percentage,
                    'completed_at' => $r->completed_at,
                ]);
            }
        }

        //
        // --- GAME SECTION ---
        //
        if (Schema::hasTable('game_scores') || Schema::hasTable('game_score')) {
            $gameTable = Schema::hasTable('game_scores') ? 'game_scores' : 'game_score';
            // detect correct user column
            $gameUserCol = Schema::hasColumn($gameTable, 'student_id') ? 'student_id' : (Schema::hasColumn($gameTable, 'user_id') ? 'user_id' : 'user_id');

            $avgGameScore = (float) DB::table($gameTable)
                ->where($gameUserCol, $studentId)
                ->avg('score') ?? 0;

            $totalGames = DB::table($gameTable)
                ->where($gameUserCol, $studentId)
                ->count();

            // best game name (join to games/games table)
            $gameMetaTable = Schema::hasTable('games') ? 'games' : (Schema::hasTable('game') ? 'game' : null);
            if ($gameMetaTable) {
                $bestGame = DB::table($gameTable . ' as s')
                    ->join($gameMetaTable . ' as g', 's.game_id', '=', 'g.id')
                    ->where("s.$gameUserCol", $studentId)
                    ->orderBy('s.score', 'desc')
                    ->limit(1)
                    ->value('g.name') ?? 'N/A';
            }

            // Determine timestamp column for games (created_at, updated_at, etc.)
            $gameTimestampCol = null;
            foreach (['completed_at', 'created_at', 'updated_at'] as $col) {
                if (Schema::hasColumn($gameTable, $col)) {
                    $gameTimestampCol = $col;
                    break;
                }
            }

            // build select list (title requires join if meta exists)
            $gameSelect = ['s.score', 's.game_id'];
            if ($gameTimestampCol) {
                $gameSelect[] = 's.' . $gameTimestampCol . ' as completed_at';
            }

            // if game meta exists, join to get a readable title
            if ($gameMetaTable) {
                $recentGameRows = DB::table($gameTable . ' as s')
                    ->join($gameMetaTable . ' as g', 's.game_id', '=', 'g.id')
                    ->where("s.$gameUserCol", $studentId)
                    ->orderBy('s.' . ($gameTimestampCol ?? 'id'), 'desc')
                    ->limit(6)
                    ->select(array_merge(['g.name as title', 's.score'], ($gameTimestampCol ? ['s.' . $gameTimestampCol . ' as completed_at'] : [])))
                    ->get();

                foreach ($recentGameRows as $r) {
                    $recentCollection->push((object)[
                        'title' => $r->title ?? ('Game #' . $r->game_id),
                        'score' => (float) $r->score,
                        'completed_at' => property_exists($r, 'completed_at') ? $r->completed_at : null,
                    ]);
                }
            } else {
                // no game meta table, just pull rows
                $recentGameRows = DB::table($gameTable . ' as s')
                    ->where("s.$gameUserCol", $studentId)
                    ->orderBy($gameTimestampCol ?? 'id', 'desc')
                    ->limit(6)
                    ->get(array_filter($gameSelect));

                foreach ($recentGameRows as $r) {
                    $recentCollection->push((object)[
                        'title' => isset($r->game_id) ? 'Game #' . $r->game_id : 'Game',
                        'score' => (float) $r->score,
                        'completed_at' => property_exists($r, 'completed_at') ? $r->completed_at : null,
                    ]);
                }
            }
        }

        //
        // --- Combine & sort recent data: keep most recent 6 by completed_at (nulls go last)
        //
        $recentData = $recentCollection
            ->sortByDesc(function ($row) {
                // normalized key: if completed_at null, return very old timestamp so nulls go last
                return $row->completed_at ? strtotime($row->completed_at) : 0;
            })
            ->values()
            ->take(6);

        // Prepare chart arrays
        // keep chart stable even when no data is available
        if ($recentData->isEmpty()) {
            $labels = ['Tiada data'];
            $scores = [0];
        } else {
            $labels = $recentData->pluck('title')->all();
            $scores = $recentData->pluck('score')->all();
        }

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
