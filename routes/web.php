<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::get('/reports/students-by-class/{class}', [ReportController::class, 'studentsByClass'])
    ->name('reports.students.byClass');

Route::get('/reports/student/{id}', [ReportController::class, 'studentReport'])->name('reports.student');

// Exports for student
Route::get('/reports/student/{id}/export/csv', [ReportController::class, 'exportStudentCsv'])->name('reports.student.csv');
Route::get('/reports/student/{id}/export/print', [ReportController::class, 'exportStudentPrintable'])->name('reports.student.print');
Route::get('/reports/student/{id}/export/excel', [ReportController::class, 'exportStudentExcel'])->name('reports.student.excel');
Route::get('/reports/student/{id}/export/pdf', [ReportController::class, 'exportStudentPdf'])->name('reports.student.pdf');

// Class report
Route::get('/reports/class', [ReportController::class, 'classIndex'])->name('reports.class');
Route::get('/reports/class/{class}/export/csv', [ReportController::class, 'exportClassCsv'])->name('reports.class.csv');
Route::get('/reports/class/{class}/export/pdf', [ReportController::class, 'exportClassPdf'])->name('reports.class.pdf');

// Optional list pages (kept)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/students', [ReportController::class, 'studentsPerformance'])->name('reports.students');
    Route::get('/reports/students/export-csv', [ReportController::class, 'exportStudentsCsv'])->name('reports.students.csv');
    Route::get('/reports/students/chart-data', [ReportController::class, 'studentsChartData'])->name('reports.students.chart');
});
