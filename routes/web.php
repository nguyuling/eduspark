<?php

use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return redirect('/forum');
});

Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/create', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');

Route::get('/forum/{id}', [ForumController::class, 'show'])->name('forum.show');
Route::get('/forum/{id}/edit', [ForumController::class, 'edit'])->name('forum.edit');
Route::put('/forum/{id}', [ForumController::class, 'update'])->name('forum.update');
Route::delete('/forum/{id}', [ForumController::class, 'destroy'])->name('forum.destroy');

Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');
