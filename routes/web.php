<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'getPosts'])->name('api.posts');

    Route::middleware('auth')->group(function () {
        Route::get('/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('/create', [PostController::class, 'store'])->name('posts.store');
        Route::post('/{post}/like', [PostController::class, 'like'])->name('posts.like');
    });
});

Route::get('/authors', [PostController::class, 'getAuthors'])->name('api.authors');


require __DIR__.'/auth.php';
