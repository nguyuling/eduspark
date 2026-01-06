use App\Http\Controllers\UserController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\GameTeacherController;
use Illuminate\Http\Request;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    // Teacher games API (JSON)
    Route::prefix('teacher')->group(function () {
        Route::get('/games', [GameTeacherController::class, 'index']);
        Route::post('/games', [GameTeacherController::class, 'store']);
        Route::put('/games/{id}', [GameTeacherController::class, 'update']);
        Route::delete('/games/{id}', [GameTeacherController::class, 'destroy']);
        Route::post('/games/{id}/restore', [GameTeacherController::class, 'restore']);
    });
});

// Lesson API routes (protected by session auth)
Route::middleware('auth:web')->group(function () {
    Route::get('/lessons', [LessonController::class, 'index']);
    Route::post('/lessons', [LessonController::class, 'store']);
    Route::put('/lessons/{id}', [LessonController::class, 'update']);
    Route::post('/lessons/{id}', [LessonController::class, 'update']); // For _method=PUT workaround
    Route::post('/lessons/{id}/delete', [LessonController::class, 'destroy']);
    Route::get('/lessons/{id}/preview', [LessonController::class, 'preview']);
});
