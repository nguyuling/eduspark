<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; // If you have controllers

Route::get('/', function () {
    return view('welcome');
});

// Basic route examples
Route::get('/about', function () {
    return 'About Page';
});

Route::get('/contact', function () {
    return 'Contact Page';
});

// Route with parameters
Route::get('/user/{id}', function ($id) {
    return "User {$id}";
});

// In routes/web.php or routes/api.php
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::post('/courses', [CourseController::class, 'store']);