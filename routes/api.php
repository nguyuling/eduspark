<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LessonController;

Route::apiResource('lessons', LessonController::class);
Route::get('/lessons/{id}/download', [LessonController::class, 'download']);
Route::put('/lessons/{id}', [LessonController::class, 'update']);
Route::delete('/lessons/{id}', [LessonController::class, 'destroy']);
