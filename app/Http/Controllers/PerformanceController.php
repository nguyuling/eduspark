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
        // Redirect teachers to reports page
        if (Auth::check() && Auth::user()->role === 'teacher') {
            return redirect()->route('reports.index');
        }
        
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
        $rawScores = [];

        $recentCollection = collect();

        //
        // --- QUIZ SECTION ---
        //
        if (Schema::hasTable('quiz_attempts') || Schema::hasTable('quiz_attempt')) {
            $quizTable = Schema::hasTable('quiz_attempts') ? 'quiz_attempts' : 'quiz_attempt';
            $quizUserCol = Schema::hasColumn($quizTable, 'student_id') ? 'student_id' : (Schema::hasColumn($quizTable, 'user_id') ? 'user_id' : 'user_id');

            // Map quiz titles and max_points if available
            $quizMetaTable = Schema::hasTable('quizzes') ? 'quizzes' : (Schema::hasTable('quiz') ? 'quiz' : null);
            $quizData = $quizMetaTable 
                ? DB::table($quizMetaTable)->select('id', 'title', 'max_points')->get()->keyBy('id')
                : collect();
            $quizTitles = $quizData->pluck('title', 'id');

            // Pull all attempts for this user with quiz data
            $allQuizAttempts = DB::table($quizTable . ' as qa')
                ->when($quizMetaTable, function ($q) use ($quizMetaTable) {
                    return $q->join($quizMetaTable . ' as q', 'qa.quiz_id', '=', 'q.id')
                        ->select('qa.quiz_id', 'qa.score');
                })
                ->where("qa.$quizUserCol", $studentId)
                ->whereNotNull('qa.submitted_at')
                ->get();

            // Preload all quiz max points at once
            $quizIds = collect($allQuizAttempts)->pluck('quiz_id')->unique();
            $quizMaxPointsData = [];
            if ($quizIds->count() > 0) {
                $maxPointsRows = DB::table('questions')
                    ->whereIn('quiz_id', $quizIds)
                    ->groupBy('quiz_id')
                    ->select('quiz_id', DB::raw('SUM(points) as total_points'))
                    ->get();
                foreach ($maxPointsRows as $row) {
                    $quizMaxPointsData[$row->quiz_id] = (int)$row->total_points;
                }
            }

            $normalizedQuizScores = [];
            $quizAggregates = [];
            foreach ($allQuizAttempts as $a) {
                // Get max_points from questions table sum
                $maxScore = $quizMaxPointsData[$a->quiz_id] ?? 0;
                if ($maxScore && $maxScore > 0) {
                    $normalizedQuizScores[] = ($a->score / $maxScore) * 100;
                } else {
                    $normalizedQuizScores[] = (float) $a->score; // fallback to raw
                }

                // aggregate per quiz for weakest topic logic
                if (!isset($quizAggregates[$a->quiz_id])) {
                    $quizAggregates[$a->quiz_id] = ['sum' => 0, 'count' => 0, 'max_points' => $maxScore];
                }
                $quizAggregates[$a->quiz_id]['sum'] += (float) $a->score;
                $quizAggregates[$a->quiz_id]['count'] += 1;
            }

            $avgQuizScore = count($normalizedQuizScores)
                ? round(array_sum($normalizedQuizScores) / count($normalizedQuizScores), 2)
                : 0;

            $totalQuizzes = DB::table($quizTable)
                ->where($quizUserCol, $studentId)
                ->whereNotNull('submitted_at')
                ->count();

            // weakest topic: pick the lowest average percent
            $weakTopic = 'Tiada';
            $weakTopicScore = null;
            foreach ($quizAggregates as $quizId => $agg) {
                if ($agg['count'] === 0) {
                    continue;
                }
                $maxScore = $agg['max_points'] ?? 0;
                // If no max_points available, use raw score (fallback)
                $avgPercent = 0;
                if ($maxScore > 0) {
                    $avgPercent = round((($agg['sum'] / $agg['count']) / $maxScore) * 100, 2);
                } else {
                    // Use raw average if max_points not available
                    $avgPercent = round($agg['sum'] / $agg['count'], 2);
                }
                // Pick the topic with the lowest score
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

            // Fetch recent quiz rows
            $recentQuizRows = DB::table($quizTable . ' as a')
                ->join($quizMetaTable . ' as q', 'a.quiz_id', '=', 'q.id')
                ->where("a.$quizUserCol", $studentId)
                ->whereNotNull('a.submitted_at')
                ->orderBy('a.' . ($quizTimestampCol ?? 'id'), 'asc') // chronological order (oldest first)
                ->limit(6)
                ->select([
                    'a.score', 
                    'a.quiz_id',
                    'q.title as quiz_title',
                    $quizTimestampCol ? 'a.' . $quizTimestampCol . ' as completed_at' : DB::raw('NULL as completed_at')
                ])
                ->get();

            // normalize and push into recentCollection with a standard completed_at
            foreach ($recentQuizRows as $r) {
                $completedAt = property_exists($r, 'completed_at') ? $r->completed_at : null;
                $fullTitle = $r->quiz_title ?? ($r->quiz_id ? 'Kuiz #' . $r->quiz_id : 'Kuiz');
                $shortTitle = Str::limit($fullTitle, 18, '…');
                $maxScore = $quizMaxPointsData[$r->quiz_id] ?? 0;
                
                // Always convert to percentage (0-100%)
                // If maxScore available, use it; otherwise assume score is already a percentage
                $scorePercent = 0;
                if ($maxScore && $maxScore > 0) {
                    $scorePercent = round(($r->score / $maxScore) * 100, 2);
                } elseif ($r->score >= 0) {
                    // Fallback: if no max_score but we have a score, assume it's already a percentage or normalize it
                    $scorePercent = (float) $r->score;
                }
                
                $recentCollection->push((object)[
                    'type' => 'quiz',
                    'title' => $shortTitle,
                    'title_full' => $fullTitle,
                    'score' => (float) $scorePercent,  // This is always 0-100%
                    'raw_score' => (float) $r->score,
                    'max_score' => (float) $maxScore,
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
                    ->value('g.title') ?? 'N/A';
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
                    ->orderBy('s.' . ($gameTimestampCol ?? 'id'), 'asc')
                    ->limit(6)
                    ->select(array_merge(['g.title as title', 's.score'], ($gameTimestampCol ? ['s.' . $gameTimestampCol . ' as completed_at'] : [])))
                    ->get();

                foreach ($recentGameRows as $r) {
                    $recentCollection->push((object)[
                        'type' => 'game',
                        'title' => Str::limit($r->title ?? ('Game #' . $r->game_id), 18, '…'),
                        'title_full' => $r->title ?? ('Game #' . $r->game_id),
                        'score' => (float) $r->score,
                        'raw_score' => (float) $r->score,
                        'max_score' => 100, // Games typically use percentage scores
                        'completed_at' => property_exists($r, 'completed_at') ? $r->completed_at : null,
                    ]);
                }
            } else {
                // no game meta table, just pull rows
                $recentGameRows = DB::table($gameTable . ' as s')
                    ->where("s.$gameUserCol", $studentId)
                    ->orderBy($gameTimestampCol ?? 'id', 'asc')
                    ->limit(6)
                    ->get(array_filter($gameSelect));

                foreach ($recentGameRows as $r) {
                    $recentCollection->push((object)[
                        'type' => 'game',
                        'title' => Str::limit(isset($r->game_id) ? 'Game #' . $r->game_id : 'Game', 18, '…'),
                        'title_full' => isset($r->game_id) ? 'Game #' . $r->game_id : 'Game',
                        'score' => (float) $r->score,
                        'raw_score' => (float) $r->score,
                        'max_score' => 100, // Games typically use percentage scores
                        'completed_at' => property_exists($r, 'completed_at') ? $r->completed_at : null,
                    ]);
                }
            }
        }

        //
        // --- Combine & sort recent data: quizzes only, oldest 6 chronologically
        //
        $recentData = $recentCollection
            ->filter(function ($row) {
                // Only include quizzes in the trend graph
                return $row->type === 'quiz';
            })
            ->sortBy(function ($row) {
                // Sort chronologically: if completed_at null, push to end
                return $row->completed_at ? strtotime($row->completed_at) : PHP_INT_MAX;
            })
            ->values()
            ->take(6);

        // Prepare chart arrays
        $labels = $recentData->pluck('title')->all();
        $labelsFull = $recentData->pluck('title_full')->all();
        $scores = $recentData->pluck('score')->all();
        $rawScores = $recentData->map(function($item) {
            return [
                'raw' => $item->raw_score ?? null,
                'max' => $item->max_score ?? null
            ];
        })->all();

        return view('performance.index', [
            'avgQuizScore' => round($avgQuizScore ?? 0, 2),
            'totalGames' => $totalGames,
            'totalQuizzes' => $totalQuizzes,
            'weakTopic' => $weakTopic ?? 'N/A',
            'bestGame' => $bestGame ?? 'N/A',
            'labels' => $labels,
            'labelsFull' => $labelsFull,
            'scores' => $scores,
            'rawScores' => $rawScores,
        ]);
    }
}
