<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Albums (replaces photos)
    Route::resource('albums', AlbumController::class);
    Route::post('albums/{album}/train', [AlbumController::class, 'startTraining'])->name('albums.train');
    
    // Photos within albums (for individual photo management)
    Route::resource('albums.photos', PhotoController::class)->shallow();

    // Alias top-level photos routes for tests expecting photos.*
    Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
    Route::get('/photos/create', [PhotoController::class, 'create'])->name('photos.create');
    Route::post('/photos', [PhotoController::class, 'store'])->name('photos.store');
    
    // Themes
    Route::resource('themes', ThemeController::class)->only(['index']);
    
    // Generations
    Route::resource('generations', GenerationController::class);
    
    // Training
    Route::get('/training/{trainingSession}', [TrainingController::class, 'show'])->name('training.show');
});

require __DIR__.'/auth.php';

// Admin routes for theme management
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('themes', ThemeController::class)->except(['index', 'show']);
});
