<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// DEBUG ROUTE - Test database connection
Route::get('/debug-db', function() {
    try {
        // Test 1: Check DB connection
        DB::connection()->getPdo();
        echo "✅ Database connected<br>";
        
        // Test 2: Check game_scores table
        $columns = DB::select('DESCRIBE game_scores');
        echo "✅ Table exists. Columns:<br>";
        foreach ($columns as $col) {
            echo "&nbsp;&nbsp;- {$col->Field} ({$col->Type})<br>";
        }
        
        // Test 3: Try to insert
        $testId = DB::table('game_scores')->insertGetId([
            'user_id' => 'debug_test_' . time(),
            'game_id' => 4,
            'score' => 99,
            'time_taken' => 60,
            'game_stats' => json_encode(['debug' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Insert test successful! ID: {$testId}<br>";
        
        // Test 4: Count records
        $count = DB::table('game_scores')->count();
        echo "✅ Total scores: {$count}<br>";
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "<br>";
        echo "❌ Trace: " . $e->getTraceAsString() . "<br>";
    }
});

// ========== GAME SCORE SAVING ROUTE ==========
// ========== SIMPLE GAME SCORE SAVING ==========
Route::post('/save-game-score', function () {
    // Get raw input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Log for debugging
    file_put_contents(storage_path('logs/game_scores.log'), 
        date('Y-m-d H:i:s') . " - " . print_r($data, true) . "\n", 
        FILE_APPEND
    );
    
    try {
        // Basic validation
        if (!$data || !isset($data['user_id'], $data['game_id'], $data['score'])) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required fields'
            ], 400);
        }
        
        // Prepare data
        $insertData = [
            'user_id' => $data['user_id'],
            'game_id' => intval($data['game_id']),
            'score' => intval($data['score']),
            'time_taken' => isset($data['time_taken']) ? intval($data['time_taken']) : 0,
            'game_stats' => isset($data['game_stats']) ? json_encode($data['game_stats']) : '{}',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        // Insert using raw SQL if DB facade fails
        $id = DB::insertGetId("
            INSERT INTO game_scores 
            (user_id, game_id, score, time_taken, game_stats, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            $insertData['user_id'],
            $insertData['game_id'],
            $insertData['score'],
            $insertData['time_taken'],
            $insertData['game_stats'],
            $insertData['created_at'],
            $insertData['updated_at']
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Score saved!',
            'score_id' => $id
        ]);
        
    } catch (\Exception $e) {
        // Log error
        file_put_contents(storage_path('logs/game_errors.log'), 
            date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n" . 
            $e->getTraceAsString() . "\n\n", 
            FILE_APPEND
        );
        
        return response()->json([
            'success' => false,
            'message' => 'Server error. Check logs.',
            'debug_error' => $e->getMessage() // Remove in production
        ], 500);
    }
});