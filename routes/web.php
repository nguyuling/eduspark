<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\Api\LessonController;

// Default route (optional — choose one)
Route::get('/', function () {
    return redirect('/lessons-table'); // redirect to lessons table instead of welcome
});

// Performance page route
Route::get('/performance', [PerformanceController::class, 'index']);

// Lessons table (Blade view)
Route::get('/lessons-table', function () {
    return view('lessons');
});

// API routes for LessonController
Route::prefix('api')->group(function () {
    // List lessons (supports query params for search & filter)
    Route::get('/lessons', [LessonController::class, 'index']);

    // Create lesson
    Route::post('/lessons', [LessonController::class, 'store']);

    // Update lesson (POST with _method=PUT from the frontend)
    Route::post('/lessons/{id}', [LessonController::class, 'update']);

    // Delete lesson (POST)
    Route::post('/lessons/{id}/delete', [LessonController::class, 'destroy']);

    // Preview metadata (returns public URL + mime)
    Route::get('/lessons/{id}/preview', [LessonController::class, 'preview']);
});

// Web route for secure download (returns file blob for download)
Route::get('/lessons/download/{id}', [App\Http\Controllers\Api\LessonController::class, 'download'])
    ->name('lessons.download');

// Extra fallback if needed
Route::fallback(function () {
    return redirect('/');
});
