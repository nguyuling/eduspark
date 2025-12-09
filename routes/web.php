<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\QuizController; // Student Controller
use App\Http\Controllers\TeacherController; // Teacher Controller
use App\Http\Controllers\DashboardController; // Central Redirection
use App\Http\Controllers\Teacher\QuizManagementController; // Import the specific controller for results

// --- 1. AUTHENTICATION ROUTES ---
require __DIR__.'/auth.php'; 

// --- 2. CENTRAL DASHBOARD ROUTE ---
Route::middleware(['auth'])->get('/home', [DashboardController::class, 'index'])->name('home');

// --- 3. STUDENT ROUTES (Protected) ---
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/quizzes', [QuizController::class, 'index'])->name('student.quizzes.index');
    
    // Start Attempt
    Route::get('/quizzes/{quiz}/attempt', [QuizController::class, 'start'])->name('student.quizzes.attempt.start');
    
    // Submit Quiz (FIXED in previous response)
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('student.quizzes.submit');
    
    // View Result
    Route::get('/attempts/{attempt}', [QuizController::class, 'showResult'])->name('student.quizzes.result');
    
    Route::delete('/attempts/{attempt}', [QuizController::class, 'quit'])->name('student.quizzes.quit');
});

// --- 4. TEACHER ROUTES (Protected) ---
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    // CRUD Routes
    Route::get('/quizzes', [TeacherController::class, 'index'])->name('quizzes.index'); 
    Route::get('/quizzes/create', [TeacherController::class, 'create'])->name('quizzes.create'); 
    Route::post('/quizzes', [TeacherController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}/edit', [TeacherController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [TeacherController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [TeacherController::class, 'destroy'])->name('quizzes.destroy');
    
    // Results Routes (Using the dedicated controller and the name the Blade view expects)
    // NOTE: The previous conflicting route 'viewAllResults' has been removed/replaced.
    Route::get('/quizzes/{quiz}/results', [QuizManagementController::class, 'index'])->name('quizzes.results'); 
    
    Route::put('/attempts/{attempt}/remark', [TeacherController::class, 'addRemark'])->name('attempt.remark');

    // NOTE: The route name in the Blade file is 'teacher.quizzes.results'. 
    // Since this is inside the 'teacher.' name group, defining it as 'quizzes.results'
    // correctly results in the full name 'teacher.quizzes.results'.
});


// --- 5. CORE MODULE ROUTES (Placeholders) ---
// These routes are used by the navigation bar and point to placeholder views.
Route::middleware(['auth'])->group(function () {
    
    // LESSONS (Accessible to all authenticated users)
    Route::get('/lessons', function () { return view('lessons.index'); })->name('lessons.index');
    
    // FORUM (Accessible to all authenticated users)
    Route::get('/forum', function () { return view('forum.index'); })->name('forum.index');

    // GAME (Accessible only to students)
    Route::get('/games', function () { return view('games.index'); })->name('games.index')->middleware('role:student');

    // PERFORMANCE (Separate views based on role)
    Route::get('/performance/teacher', function () { return view('performance.teacher_view'); })->name('performance.teacher_view')->middleware('role:teacher');
    Route::get('/performance/student', function () { return view('performance.student_view'); })->name('performance.student_view')->middleware('role:student');
});


// --- 6. ROOT REDIRECTION ---
// This handles the primary entry point:
Route::get('/', function () {
    if (Auth::check()) {
        // If logged in, send them to the home route which handles role-based redirection
        return redirect()->route('home');
    }
    // If not logged in, send them to the login form
    return redirect()->route('login');
})->middleware('web');