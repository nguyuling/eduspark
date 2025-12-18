<?php

use App\Http\Controllers\ForumController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizTeacherController;
use App\Http\Controllers\QuizStudentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\PerformanceController;
use Illuminate\Support\Facades\Route;

// Include authentication routes
require __DIR__ . '/auth.php';

Route::get('/', function() {
    if (auth()->check()) {
        $user = auth()->user();
        return redirect('/performance');
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
