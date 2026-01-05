<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizTeacherController;
use App\Http\Controllers\QuizStudentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameTeacherController;
use Illuminate\Support\Facades\Route;

// Include authentication routes
require __DIR__ . '/auth.php';

Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        // Teachers land on reports, others on performance
        if (($user->role ?? null) === 'teacher') {
            return redirect()->route('reports.index');
        }
        return redirect()->route('performance');
    }
    return redirect('/login');
})->name('home');

// Profile routes (authenticated only)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/edit-password', [UserController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');
});

// Lesson routes (authenticated only)
Route::middleware('auth')->group(function () {
    Route::get('/lesson', [LessonController::class, 'index'])->name('lesson.index');
    Route::get('/lesson/create', [LessonController::class, 'create'])->name('lesson.create');
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons.index');
    Route::get('/lesson/{id}', [LessonController::class, 'show'])->name('lesson.show');
    Route::get('/lesson/{id}/edit', [LessonController::class, 'edit'])->name('lesson.edit');
    Route::post('/lesson', [LessonController::class, 'store'])->name('lesson.store');
    Route::put('/lesson/{id}', [LessonController::class, 'update'])->name('lesson.update');
    Route::delete('/lesson/{id}', [LessonController::class, 'destroy'])->name('lesson.destroy');
    Route::get('/lesson/{id}/preview', [LessonController::class, 'preview'])->name('lesson.preview');
    Route::get('/lesson/{id}/download', [LessonController::class, 'downloadLesson'])->name('lesson.download');
    Route::get('/lesson/{id}/preview-file', [LessonController::class, 'previewFile'])->name('lesson.preview-file');
});

// Quiz Teacher routes
Route::middleware('auth')->group(function () {
    Route::get('/teacher/quizzes', [QuizTeacherController::class, 'index'])->name('teacher.quizzes.index');
    Route::get('/teacher/quizzes/create', [QuizTeacherController::class, 'create'])->name('teacher.quizzes.create');
    Route::post('/teacher/quizzes', [QuizTeacherController::class, 'store'])->name('teacher.quizzes.store');
    Route::get('/teacher/quizzes/{quiz}', [QuizTeacherController::class, 'show'])->name('teacher.quizzes.show');
    Route::get('/teacher/quizzes/{quiz}/results', [QuizTeacherController::class, 'showResults'])->name('teacher.quizzes.results');
    Route::get('/teacher/quizzes/{quiz}/edit', [QuizTeacherController::class, 'edit'])->name('teacher.quizzes.edit');
    Route::put('/teacher/quizzes/{quiz}', [QuizTeacherController::class, 'update'])->name('teacher.quizzes.update');
    Route::delete('/teacher/quizzes/{quiz}', [QuizTeacherController::class, 'destroy'])->name('teacher.quizzes.destroy');
});

// Quiz Student routes
Route::middleware('auth')->group(function () {
    Route::get('/quizzes', [QuizStudentController::class, 'index'])->name('student.quizzes.index');
    Route::get('/quizzes/{quiz}/start', [QuizStudentController::class, 'start'])->name('student.quizzes.start');
    Route::post('/quizzes/{quiz}/submit', [QuizStudentController::class, 'submit'])->name('student.quizzes.submit');
    Route::get('/quizzes/{attempt}/quit', [QuizStudentController::class, 'quit'])->name('student.quizzes.quit');
    Route::get('/quizzes/{attempt}/result', [QuizStudentController::class, 'showResult'])->name('student.quizzes.result');
});

// Performance routes
Route::middleware('auth')->group(function () {
    Route::get('/performance', [PerformanceController::class, 'index'])->name('performance');
});

// Report routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Students dropdown + detail
    Route::get('/reports/students-by-class/{class}', [ReportController::class, 'studentsByClass'])
        ->name('reports.students.byClass');
    Route::get('/reports/student/{id}', [ReportController::class, 'student'])
        ->name('reports.student');
    Route::get('/reports/student/{id}/export/csv', [ReportController::class, 'exportStudentCsv'])
        ->name('reports.student.csv');
    Route::get('/reports/student/{id}/export/print', [ReportController::class, 'exportStudentPdf'])
        ->name('reports.student.print');

    // Class reports
    Route::get('/reports/class', [ReportController::class, 'classIndex'])
        ->name('reports.class');
    Route::get('/reports/class/{class}/export/csv', [ReportController::class, 'exportClassCsv'])
        ->name('reports.class.csv');
    Route::get('/reports/class/{class}/export/pdf', [ReportController::class, 'exportClassPdf'])
        ->name('reports.class.pdf');

    // Student performance (optional, kept for compatibility)
    Route::get('/reports/students', [ReportController::class, 'studentsPerformance'])
        ->name('reports.students');
    Route::get('/reports/students/export-csv', [ReportController::class, 'exportStudentsCsv'])
        ->name('reports.students.csv');
    Route::get('/reports/students/chart-data', [ReportController::class, 'studentsChartData'])
        ->name('reports.students.chart');
    
    // Statistics export
    Route::get('/reports/export-statistics', [ReportController::class, 'exportStatistics'])
        ->name('reports.statistics.export');
    
    // Statistics API
    Route::get('/api/statistics', [ReportController::class, 'getStatistics'])
        ->name('api.statistics');
});

// Games routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/games', [GameController::class, 'index'])->name('games.index');
    Route::get('/games/{id}/play', [GameController::class, 'play'])->name('games.play');
    Route::post('/games/{id}/result', [GameController::class, 'storeResult'])->name('games.storeResult');
    Route::get('/games/{id}/result', [GameController::class, 'result'])->name('games.result');
    Route::put('/games/{id}', [GameController::class, 'update'])->name('games.update');
    Route::delete('/games/{id}', [GameController::class, 'destroy'])->name('games.destroy');
    Route::post('/games/{id}/restore', [GameController::class, 'restore'])->name('games.restore');
    Route::get('/games/{id}/leaderboard', [GameController::class, 'leaderboard'])->name('games.leaderboard');
    
    // Rewards routes
    Route::get('/rewards', [GameController::class, 'myRewards'])->name('rewards.index');
    Route::post('/rewards/{id}/claim', [GameController::class, 'claimReward'])->name('rewards.claim');
    
    // Teacher games routes
    Route::get('/teacher/games/create', [GameTeacherController::class, 'create'])->name('teacher.games.create');
    Route::post('/teacher/games', [GameTeacherController::class, 'store'])->name('teacher.games.store');
    Route::get('/teacher/games/{id}/edit', [GameTeacherController::class, 'edit'])->name('teacher.games.edit');
    Route::put('/teacher/games/{id}', [GameTeacherController::class, 'update'])->name('teacher.games.update');
    Route::delete('/teacher/games/{id}', [GameTeacherController::class, 'destroy'])->name('teacher.games.destroy');
});

// Forum routes
Route::middleware('auth')->group(function () {
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::get('/forum/{id}', [ForumController::class, 'show'])->name('forum.show');
    Route::get('/forum/{id}/edit', [ForumController::class, 'edit'])->name('forum.edit');
    Route::put('/forum/{id}', [ForumController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{id}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');
});

// OLD LEGACY ROUTES REMOVED - All games now go through GameController@play
// This ensures the game summary and leaderboard flow works properly

// Messages routes for chat functionality
Route::middleware('auth')->group(function () {
    Route::get('/messages', [ForumController::class, 'getMessages'])->name('messages.index');
    Route::get('/messages/conversation/{userId}', [ForumController::class, 'getConversation'])->name('messages.conversation');
    Route::post('/messages/send', [ForumController::class, 'sendMessage'])->name('messages.send');
});

// AI Assistant Chat routes (isolated)
Route::middleware('auth')->group(function () {
    Route::post('/api/ai-chat/send', [\App\Http\Controllers\AIChatController::class, 'sendMessage'])->name('ai.chat.send');
});
