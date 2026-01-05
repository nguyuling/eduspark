<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Paste this file into routes/web.php — it preserves your original URIs
| and route names but points to the robust controller methods.
|
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
});

// Performance Page (your original)
Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');

//
// =====================
// REPORT ROUTES (compatible)
// =====================
//
// Landing page for reports
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// AJAX: Students by class (used by reports/index.blade.js)
Route::get('/reports/students-by-class/{class}', [ReportController::class, 'studentsByClass'])
    ->name('reports.students.byClass');

// Student individual report + exports
// Your blades and JS use the following route names/URIs — keep them the same.
Route::get('/reports/student/{id}', [ReportController::class, 'student'])
    ->name('reports.student');

// CSV & printable export for a student (names used in blades)
Route::get('/reports/student/{id}/export/csv', [ReportController::class, 'exportStudentCsv'])
    ->name('reports.student.csv');

Route::get('/reports/student/{id}/export/print', [ReportController::class, 'exportStudentPdf'])
    ->name('reports.student.print');

// Class report + exports (expects ?class=Name or named param)
Route::get('/reports/class', [ReportController::class, 'classIndex'])
    ->name('reports.class');

// Exports for class (URI pattern kept exactly as before)
Route::get('/reports/class/{class}/export/csv', [ReportController::class, 'exportClassCsv'])
    ->name('reports.class.csv');

Route::get('/reports/class/{class}/export/pdf', [ReportController::class, 'exportClassPdf'])
    ->name('reports.class.pdf');

//
// ===============================
// NEW STUDENT PERFORMANCE (optional)
// ===============================
// These routes were in your original file — I kept them, protected by auth.
Route::middleware(['auth'])->group(function () {

    // List of all students' performance + filters + chart
    Route::get('/reports/students', [ReportController::class, 'studentsPerformance'] ?? [ReportController::class, 'studentsPerformance'])
        ->name('reports.students');

    // CSV Export for student performance
    Route::get('/reports/students/export-csv', [ReportController::class, 'exportStudentsCsv'] ?? [ReportController::class, 'exportStudentsCsv'])
        ->name('reports.students.csv');

    // AJAX chart data (top students)
    Route::get('/reports/students/chart-data', [ReportController::class, 'studentsChartData'] ?? [ReportController::class, 'studentsChartData'])
        ->name('reports.students.chart');
});
