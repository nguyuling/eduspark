<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\GameTeacherController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========== PUBLIC ROUTES ==========

// Home/Index
Route::get('/', function () {
    return view('welcome');
});

// ========== DEBUG/UTILITY ROUTES ==========

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

// ========== AUTHENTICATION ROUTES ==========

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ========== PUBLIC GAME ROUTES ==========

// Game summary page (accessible without auth)
Route::get('/game-summary/{gameId}/{userId?}', function($gameId, $userId = null) {
    return view('game-summary', [
        'game_id' => $gameId,
        'user_id' => $userId,
        'game_title' => 'Permainan'
    ]);
})->name('game-summary');

// Leaderboard page (accessible without auth)
Route::get('/leaderboard/{gameId}', function($gameId) {
    return view('leaderboard', [
        'game_id' => $gameId,
        'game_title' => 'Leaderboard'
    ]);
})->name('leaderboard');

// ========== PROTECTED ROUTES (Require Authentication) ==========

Route::middleware(['auth'])->group(function () {
    
    // ========== STUDENT ROUTES ==========
    Route::middleware([function ($request, $next) {
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }])->group(function () {
        // Student dashboard
        Route::get('/dashboard', function () {
            return view('student.dashboard');
        })->name('dashboard');
        
        // Student game pages
        Route::get('/games', [GameController::class, 'index'])->name('games.index');
        Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');
        Route::post('/games/{id}/save-score', [GameController::class, 'saveScore'])->name('games.saveScore');
        
        // Student profile
        Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    });
    
    // ========== TEACHER ROUTES ==========
    Route::middleware([function ($request, $next) {
        if (auth()->user()->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }])->group(function () {
        // Teacher dashboard
        Route::get('/teacher/dashboard', function () {
            return view('teacher.dashboard');
        })->name('teacher.dashboard');
        
        // ========== TEACHER GAME MANAGEMENT ==========
        Route::prefix('teacher')->group(function () {
            // List all games created by teacher
            Route::get('/games', [GameTeacherController::class, 'index'])->name('teacher.games.index');
            
            // Create new game
            Route::get('/games/create', [GameTeacherController::class, 'create'])->name('teacher.games.create');
            Route::post('/games', [GameTeacherController::class, 'store'])->name('teacher.games.store');
            
            // Edit/Update game
            Route::get('/games/{game}/edit', [GameTeacherController::class, 'edit'])->name('teacher.games.edit');
            Route::put('/games/{game}', [GameTeacherController::class, 'update'])->name('teacher.games.update');
            
            // Delete game
            Route::delete('/games/{game}', [GameTeacherController::class, 'destroy'])->name('teacher.games.destroy');
            
            // View game details
            Route::get('/games/{game}', [GameTeacherController::class, 'show'])->name('teacher.games.show');
        });
        
        // Teacher lesson management (from TEACHER_ROUTES.md)
        Route::get('/lesson', function () {
            return view('lesson.index-teacher');
        })->name('lesson.index');
        
        Route::get('/lesson/create', function () {
            return view('lesson.create');
        })->name('lesson.create');
        
        // Teacher quiz management (if exists)
        Route::prefix('teacher')->group(function () {
            Route::get('/quizzes', function () {
                return view('teacher.quizzes.index');
            })->name('teacher.quizzes.index');
            
            Route::get('/quizzes/create', function () {
                return view('teacher.quizzes.create');
            })->name('teacher.quizzes.create');
        });
        
        // Teacher profile
        Route::get('/profile', function () {
            return view('profile.show');
        })->name('profile.show');
        
        Route::get('/profile/edit', function () {
            return view('profile.edit');
        })->name('profile.edit');
    });
    
    // ========== ADMIN ROUTES ==========
    Route::middleware([function ($request, $next) {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
});

// ========== API ROUTES (for AJAX calls) ==========
Route::prefix('api')->group(function () {
    // Public game API routes
    Route::get('/games', [GameController::class, 'apiIndex']);
    Route::get('/games/{id}', [GameController::class, 'apiShow']);
    
    // Game score saving (public for now, can be protected)
    Route::post('/games/{id}/score', [GameController::class, 'saveScore']);
    
    // GAME SUMMARY & LEADERBOARD API ROUTES - ADD THESE
    Route::get('/game-summary/{gameId}', [GameController::class, 'getGameSummary'])->name('api.game-summary');
    Route::get('/leaderboard/{gameId}', [GameController::class, 'getLeaderboard'])->name('api.leaderboard');
    
    // Collect rewards (protected)
    Route::middleware(['auth'])->group(function () {
        // Collect rewards
        Route::post('/rewards/collect', [GameController::class, 'collectRewards'])->name('api.rewards.collect');
    });
    
    // Game editing API (teacher only)
    Route::middleware(['auth'])->group(function () {
        Route::middleware([function ($request, $next) {
            if (auth()->user()->role !== 'teacher') {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        }])->group(function () {
            Route::get('/teacher/games', [GameTeacherController::class, 'apiIndex']);
            Route::post('/games', [GameController::class, 'store']);
            Route::put('/games/{id}', [GameController::class, 'update']);
            Route::delete('/games/{id}', [GameController::class, 'destroy']);
            Route::post('/games/{id}/validate', [GameController::class, 'validateGameData']);
            Route::get('/games/{id}/history', [GameController::class, 'getUpdateHistory']);
        });
    });
});

// ========== PUBLIC GAME PLAYING ROUTES ==========
Route::prefix('play')->group(function () {
    Route::get('/games', [GameController::class, 'index'])->name('play.games.index');
    Route::get('/games/{id}', [GameController::class, 'show'])->name('play.games.show');
    
    // Game summary page (public facing)
    Route::get('/games/{game}/summary', function ($gameId) {
        return view('game-summary', ['game_id' => $gameId]);
    })->name('play.games.summary');
    
    // Leaderboard page (public facing)
    Route::get('/games/{game}/leaderboard', function ($gameId) {
        return view('leaderboard', ['game_id' => $gameId]);
    })->name('play.games.leaderboard');
});

// ========== TEST ROUTES (Remove in production) ==========
Route::get('/test-game-summary/{gameId}', function ($gameId) {
    return view('test.game-summary-test', ['game_id' => $gameId]);
});

Route::get('/test-leaderboard/{gameId}', function ($gameId) {
    return view('test.leaderboard-test', ['game_id' => $gameId]);
});

// Simple test routes
Route::get('/test-summary/{id?}', function($id = 1) {
    return view('game-summary', [
        'game_id' => $id,
        'game_title' => 'Permainan Test'
    ]);
});

// ========== FALLBACK ROUTES ==========
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});