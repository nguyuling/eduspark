<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuizController; 
use App\Http\Controllers\TeacherController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\Teacher\QuizManagementController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\LessonController;

// --- 1. AUTHENTICATION ROUTES ---

// Registration Routes
Route::get('/register', fn() => view('user.register'))->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.post');

// Login Routes
Route::get('/login', fn() => view('user.login'))->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');
// ewqdew
// 🔑 NEW: LOGOUT ROUTE ADDED HERE to resolve 'Route [logout] not defined'
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/'); // Redirects to the root, which in turn redirects to /login
})->name('logout');

// Lesson download and preview routes (protected)
Route::middleware('auth')->get('/lessons/download/{id}', [LessonController::class, 'downloadLesson'])->name('lesson.download');
Route::middleware('auth')->get('/lessons/preview/{id}', [LessonController::class, 'previewFile'])->name('lesson.preview.file');


// --- 2. AUTHENTICATED USER ROUTES (Central Hub and Modules) ---

Route::middleware(['auth'])->group(function () {
    
    // 2.1. HOME ROUTE (Redirect to Performance/Prestasi page)
    Route::get('/home', function () {
        return redirect()->route('performance.student_view');
    })->name('home');

    // 2.2. PROFILE ROUTES
    Route::get('/profile', [UserController::class, 'profile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/password/edit', [UserController::class, 'editPassword'])->name('profile.password.edit');
    Route::post('/profile/password/update', [UserController::class, 'updatePassword'])->name('profile.password.update');
    
    // 2.3. CORE MODULE PLACEHOLDERS (Lessons, Forum, Game, Performance)
    Route::get('/lessons', function () { return view('lessons.index'); })->name('lessons.index');
    Route::get('/forum', function () { return view('forum.index'); })->name('forum.index');
    Route::get('/games', function () { return view('games.index'); })->name('games.index')->middleware('role:student');
    Route::get('/performance/teacher', function () { return view('performance.teacher_view'); })->name('performance.teacher_view')->middleware('role:teacher');
    Route::get('/performance/student', [PerformanceController::class, 'index']) // <--- USE YOUR CONTROLLER
        ->name('performance.student_view')
        ->middleware('role:student');});


// --- 3. STUDENT QUIZ ROUTES ---
Route::prefix('quizzes')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('student.quizzes.index');
    // Route::get('/{quiz}/attempt/{attempt}', [QuizController::class, 'showAttempt'])->name('student.quizzes.attempt.show');
    Route::get('/{quiz}/attempt', [QuizController::class, 'start'])->name('student.quizzes.attempt.start');
    Route::post('/{quiz}/submit', [QuizController::class, 'submit'])->name('student.quizzes.submit');
    // Route::delete('/{quiz}/quit', action: [QuizController::class, 'quit'])->name('student.quizzes.attempt.quit');
    Route::delete('/quit/{attempt}', [QuizController::class, 'quit'])->name('student.quizzes.attempt.quit');
    Route::get('/result/{attempt}', [QuizController::class, 'showResult'])->name('student.quizzes.result'); 
});


// --- 4. TEACHER QUIZ ROUTES ---
Route::prefix('teacher/quizzes')->middleware(['auth', 'role:teacher'])->group(function () {

    Route::get('/', [QuizManagementController::class, 'index'])->name('teacher.quizzes.index');
    Route::get('/create', [QuizManagementController::class, 'create'])->name('teacher.quizzes.create');
    Route::post('/', [QuizManagementController::class, 'store'])->name('teacher.quizzes.store');
    Route::get('/{quiz}', [QuizManagementController::class, 'show'])->name('teacher.quizzes.show');
    Route::get('/{quiz}/edit', [QuizManagementController::class, 'edit'])->name('teacher.quizzes.edit');
    Route::put('/{quiz}', [QuizManagementController::class, 'update'])->name('teacher.quizzes.update');
    Route::delete('/{quiz}', [QuizManagementController::class, 'destroy'])->name('teacher.quizzes.destroy');
    Route::get('/{quiz}/results', [QuizManagementController::class, 'results'])->name('teacher.quizzes.results');
    Route::post('/teacher/attempts/{attempt}/remark', [QuizManagementController::class, 'addRemark'])
    ->name('teacher.attempts.remark')
    ->middleware(['auth', 'role:teacher']);
});




// --- 5. ROOT REDIRECTION ---
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
})->middleware('web');