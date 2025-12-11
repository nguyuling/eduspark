<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameProgressController;

// Game Update Routes (Teacher specific)
Route::prefix('teacher')->middleware('auth:sanctum')->group(function () {
    Route::prefix('games')->group(function () {
        // Get game for editing
        Route::get('/{id}/edit', [GameController::class, 'edit']);
        
        // Update game (full update)
        Route::put('/{id}', [GameController::class, 'update']);
        
        // Partial update
        Route::patch('/{id}', [GameController::class, 'partialUpdate']);
        
        // Get update history
        Route::get('/{id}/update-history', [GameController::class, 'getUpdateHistory']);
        
        // Validate game data before update
        Route::post('/validate', [GameController::class, 'validateGameData']);
        Route::post('/{id}/validate', [GameController::class, 'validateGameData']);
    });
});

Route::prefix('games')->group(function () {
    // Public game routes
    Route::get('/', [GameController::class, 'index']);
    Route::get('/{id}', [GameController::class, 'show']);
    Route::get('/{id}/version', [GameController::class, 'getUpdateHistory']);
    Route::post('/save-score', [GameController::class, 'saveScore']);
    
    // Game Progress Routes
    Route::get('/{game}/start', [GameProgressController::class, 'startGame']);
    Route::post('/{game}/save-progress', [GameProgressController::class, 'saveProgress']);
    Route::get('/{game}/progress', [GameProgressController::class, 'getProgress']);
    Route::get('/progress/all', [GameProgressController::class, 'getAllProgress']);
    Route::post('/rewards/{reward}/claim', [GameProgressController::class, 'claimReward']);
});