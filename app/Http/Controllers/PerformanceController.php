<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        $labelsFull = [];
        $scores = [];

        $recentCollection = collect();

        //
        // --- QUIZ SECTION ---
        //
        if (Schema::hasTable('quiz_attempts') || Schema::hasTable('quiz_attempt')) {
            $quizTable = Schema::hasTable('quiz_attempts') ? 'quiz_attempts' : 'quiz_attempt';
            $quizUserCol = Schema::hasColumn($quizTable, 'student_id') ? 'student_id' : (Schema::hasColumn($quizTable, 'user_id') ? 'user_id' : 'user_id');

            // Pull all attempts for this user to compute percentage based on per-quiz max scores
            $allQuizAttempts = DB::table($quizTable)
                ->where($quizUserCol, $studentId)
                ->select('quiz_id', 'score')
                ->get();

            // Max score per quiz across all users (best available proxy for full marks)
            $maxScoreByQuiz = DB::table($quizTable)
                ->select('quiz_id', DB::raw('MAX(score) as max_score'))
                ->groupBy('quiz_id')
                ->pluck('max_score', 'quiz_id');

            // Map quiz titles if available
            $quizMetaTable = Schema::hasTable('quizzes') ? 'quizzes' : (Schema::hasTable('quiz') ? 'quiz' : null);
            $quizTitles = $quizMetaTable ? DB::table($quizMetaTable)->pluck('title', 'id') : collect();

            $normalizedQuizScores = [];
            $quizAggregates = [];
            foreach ($allQuizAttempts as $a) {
                $maxScore = $maxScoreByQuiz[$a->quiz_id] ?? null;
                if ($maxScore && $maxScore > 0) {
                    $normalizedQuizScores[] = ($a->score / $maxScore) * 100;
                } else {
                    $normalizedQuizScores[] = (float) $a->score; // fallback to raw
                }

                // aggregate per quiz for weakest topic logic
                if (!isset($quizAggregates[$a->quiz_id])) {
                    $quizAggregates[$a->quiz_id] = ['sum' => 0, 'count' => 0];
                }
                $quizAggregates[$a->quiz_id]['sum'] += (float) $a->score;
                $quizAggregates[$a->quiz_id]['count'] += 1;
            }

            $avgQuizScore = count($normalizedQuizScores)
                ? array_sum($normalizedQuizScores) / count($normalizedQuizScores)
                : 0;

            $totalQuizzes = DB::table($quizTable)
                ->where($quizUserCol, $studentId)
                ->count();

            // weakest topic: pick the lowest average percent but only if below 100%
            $weakTopic = 'Tiada';
            $weakTopicScore = null;
            foreach ($quizAggregates as $quizId => $agg) {
                $maxScore = $maxScoreByQuiz[$quizId] ?? null;
                if (!$maxScore || $maxScore <= 0 || $agg['count'] === 0) {
                    continue;
                }
                $avgPercent = (($agg['sum'] / $agg['count']) / $maxScore) * 100;
                if ($avgPercent >= 100) {
                    continue; // ignore perfect scores
                }
                if (is_null($weakTopicScore) || $avgPercent < $weakTopicScore) {
                    $weakTopicScore = $avgPercent;
                    $weakTopic = ($quizTitles[$quizId] ?? ('Kuiz #' . $quizId));
                }
            }

            // Determine a timestamp column to use for ordering/display (safe fallback)
            $quizTimestampCol = null;
            foreach (['completed_at', 'created_at', 'updated_at'] as $col) {
                if (Schema::hasColumn($quizTable, $col)) {
                    $quizTimestampCol = $col;
                    break;
                }
            }

            // Fetch recent quiz rows (only requested columns that exist)
            $quizSelect = ['a.score', 'a.quiz_id'];
            if ($quizTimestampCol) {
                $quizSelect[] = 'a.' . $quizTimestampCol . ' as completed_at';
            }
            if ($quizMetaTable) {
                $quizSelect[] = 'q.title as quiz_title';
            }

            $recentQuizRows = DB::table($quizTable . ' as a')
                ->when($quizMetaTable, function ($q) use ($quizMetaTable) {
                    return $q->leftJoin($quizMetaTable . ' as q', 'a.quiz_id', '=', 'q.id');
                })
                ->where("a.$quizUserCol", $studentId)
                ->orderBy('a.' . ($quizTimestampCol ?? 'id'), 'desc') // fallback to id if no timestamp
                ->limit(6)
                ->get($quizSelect);

            // normalize and push into recentCollection with a standard completed_at
            foreach ($recentQuizRows as $r) {
                $completedAt = property_exists($r, 'completed_at') ? $r->completed_at : null;
                $fullTitle = $r->quiz_title ?? ($r->quiz_id ? 'Kuiz #' . $r->quiz_id : 'Kuiz');
                $shortTitle = Str::limit($fullTitle, 18, '…');
                $maxScore = $maxScoreByQuiz[$r->quiz_id] ?? null;
                $scorePercent = ($maxScore && $maxScore > 0)
                    ? ($r->score / $maxScore) * 100
                    : (float) $r->score;
                $recentCollection->push((object)[
                    'title' => $shortTitle,
                    'title_full' => $fullTitle,
                    'score' => (float) $scorePercent,
                    'completed_at' => $completedAt,
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
                        'title' => Str::limit($r->title ?? ('Game #' . $r->game_id), 18, '…'),
                        'title_full' => $r->title ?? ('Game #' . $r->game_id),
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
                        'title' => Str::limit(isset($r->game_id) ? 'Game #' . $r->game_id : 'Game', 18, '…'),
                        'title_full' => isset($r->game_id) ? 'Game #' . $r->game_id : 'Game',
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
        $labels = $recentData->pluck('title')->all();
        $labelsFull = $recentData->pluck('title_full')->all();
        $scores = $recentData->pluck('score')->all();

        return view('performance.index', [
            'avgQuizScore' => round($avgQuizScore ?? 0, 1),
            'avgGameScore' => round($avgGameScore ?? 0, 1),
            'totalQuizzes' => $totalQuizzes,
            'totalGames' => $totalGames,
            'weakTopic' => $weakTopic ?? 'N/A',
            'bestGame' => $bestGame ?? 'N/A',
            'labels' => $labels,
            'labelsFull' => $labelsFull,
            'scores' => $scores,
        ]);
    }
}
