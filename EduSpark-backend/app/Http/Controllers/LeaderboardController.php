<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    /**
     * Get leaderboard with filters
     */
    public function index(Request $request)
    {
        $class = $request->query('class', 'all');
        $subject = $request->query('subject', 'all');
        $timePeriod = $request->query('timePeriod', 'all');
        $sortBy = $request->query('sortBy', 'score');

        $query = DB::table('users')
            ->leftJoin('game_scores', 'users.id', '=', 'game_scores.user_id')
            ->leftJoin('games', 'game_scores.game_id', '=', 'games.id')
            ->leftJoin('achievements', function ($join) {
                $join->on('users.id', '=', 'achievements.user_id');
            })
            ->select(
                'users.id',
                'users.name',
                'users.class',
                'games.subject',
                DB::raw('COALESCE(SUM(game_scores.score), 0) as total_score'),
                DB::raw('COUNT(DISTINCT game_scores.id) as games_played'),
                DB::raw('COUNT(DISTINCT achievements.id) as achievements_count'),
                DB::raw('MAX(game_scores.completed_at) as last_played')
            )
            ->where('users.role', 'student')
            ->groupBy('users.id', 'users.name', 'users.class', 'games.subject');

        // Apply class filter
        if ($class !== 'all') {
            $query->where('users.class', $class);
        }

        // Apply subject filter
        if ($subject !== 'all') {
            $query->where('games.subject', $subject);
        }

        // Apply time period filter
        if ($timePeriod !== 'all') {
            $startDate = $this->getStartDate($timePeriod);
            $query->where('game_scores.completed_at', '>=', $startDate);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'score':
                $query->orderBy('total_score', 'desc');
                break;
            case 'achievements':
                $query->orderBy('achievements_count', 'desc');
                break;
            case 'name':
                $query->orderBy('users.name', 'asc');
                break;
        }

        $results = $query->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count()
        ]);
    }

    /**
     * Update score after game completion
     */
    public function updateScore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'game_id' => 'required|exists:games,id',
            'score' => 'required|integer|min:0',
        ]);

        try {
            $gameScore = DB::table('game_scores')->insert([
                'user_id' => $request->user_id,
                'game_id' => $request->game_id,
                'score' => $request->score,
                'completed_at' => $request->timestamp ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Check for achievements
            $this->checkAndAwardAchievements($request->user_id, $request->game_id, $request->score);

            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully',
                'data' => $gameScore
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update score: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's specific position
     */
    public function getUserPosition($userId, Request $request)
    {
        $class = $request->query('class', 'all');
        $subject = $request->query('subject', 'all');
        $timePeriod = $request->query('timePeriod', 'all');

        $subQuery = DB::table('users')
            ->leftJoin('game_scores', 'users.id', '=', 'game_scores.user_id')
            ->leftJoin('games', 'game_scores.game_id', '=', 'games.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COALESCE(SUM(game_scores.score), 0) as total_score')
            )
            ->where('users.role', 'student')
            ->groupBy('users.id', 'users.name');

        // Apply filters to subquery
        if ($class !== 'all') {
            $subQuery->where('users.class', $class);
        }

        if ($subject !== 'all') {
            $subQuery->where('games.subject', $subject);
        }

        if ($timePeriod !== 'all') {
            $startDate = $this->getStartDate($timePeriod);
            $subQuery->where('game_scores.completed_at', '>=', $startDate);
        }

        // Get ranked users
        $rankedUsers = DB::table(DB::raw("({$subQuery->toSql()}) as ranked_users"))
            ->mergeBindings($subQuery)
            ->select('*', DB::raw('ROW_NUMBER() OVER (ORDER BY total_score DESC) as rank'))
            ->get();

        $userRank = $rankedUsers->firstWhere('id', $userId);

        return response()->json([
            'success' => true,
            'data' => $userRank
        ]);
    }

    /**
     * Reset leaderboard (teacher only)
     */
    public function reset(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|string',
            'subject_id' => 'nullable|string',
            'reset_by' => 'required|in:teacher'
        ]);

        // Check if user is authenticated and is a teacher
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            return response()->json([
                'success' => false,
                'message' => 'Only teachers can reset leaderboards'
            ], 403);
        }

        $query = DB::table('game_scores');

        if ($request->filled('class_id')) {
            $query->whereIn('user_id', function ($subQuery) use ($request) {
                $subQuery->select('id')
                    ->from('users')
                    ->where('class', $request->class_id);
            });
        }

        if ($request->filled('subject_id')) {
            $query->whereIn('game_id', function ($subQuery) use ($request) {
                $subQuery->select('id')
                    ->from('games')
                    ->where('subject', $request->subject_id);
            });
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Leaderboard reset successfully',
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Get available classes for filter dropdown
     */
    public function getAvailableClasses()
    {
        $classes = DB::table('users')
            ->where('role', 'student')
            ->whereNotNull('class')
            ->select('class')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Get available subjects for filter dropdown
     */
    public function getAvailableSubjects()
    {
        $subjects = DB::table('games')
            ->select('subject')
            ->distinct()
            ->orderBy('subject')
            ->pluck('subject');

        return response()->json([
            'success' => true,
            'data' => $subjects
        ]);
    }

    /**
     * Helper function to get start date based on time period
     */
    private function getStartDate($timePeriod)
    {
        $now = Carbon::now();

        switch ($timePeriod) {
            case 'today':
                return $now->startOfDay();
            case 'week':
                return $now->subWeek();
            case 'month':
                return $now->subMonth();
            case 'year':
                return $now->subYear();
            default:
                return Carbon::createFromTimestamp(0); // Beginning of time
        }
    }

    /**
     * Check and award achievements based on score
     */
    private function checkAndAwardAchievements($userId, $gameId, $score)
    {
        // High score achievements
        if ($score >= 100) {
            $this->awardAchievement($userId, 'high_score_100', 'Scored 100+ points in a game');
        }
        
        if ($score >= 200) {
            $this->awardAchievement($userId, 'high_score_200', 'Scored 200+ points in a game');
        }
        
        if ($score >= 300) {
            $this->awardAchievement($userId, 'high_score_300', 'Scored 300+ points in a game');
        }

        // Check for perfect game
        $game = DB::table('games')
            ->select('max_score')
            ->where('id', $gameId)
            ->first();

        if ($game && $score >= $game->max_score) {
            $this->awardAchievement($userId, 'perfect_game', 'Achieved perfect score in a game');
        }

        // Check for daily streak
        $todayGamesCount = DB::table('game_scores')
            ->where('user_id', $userId)
            ->whereDate('completed_at', Carbon::today())
            ->count();

        if ($todayGamesCount >= 3) {
            $this->awardAchievement($userId, 'daily_streak_3', 'Played 3 games in one day');
        }
    }

    /**
     * Award achievement to user
     */
    private function awardAchievement($userId, $achievementCode, $description)
    {
        // Check if achievement already exists
        $exists = DB::table('achievements')
            ->where('user_id', $userId)
            ->where('achievement_code', $achievementCode)
            ->exists();

        if (!$exists) {
            DB::table('achievements')->insert([
                'user_id' => $userId,
                'achievement_code' => $achievementCode,
                'description' => $description,
                'awarded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}