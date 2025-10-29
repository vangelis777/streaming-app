<?php

use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// THIS IS THE FIX:
// 1. Point the homepage (/) to the 'index' method.
Route::get('/', [DiscoverController::class, 'index'])->name('discover');

// 2. Redirect the old /discover URL to the homepage.
Route::get('/discover', function () {
    return redirect('/');
});

// 3. The API route for search suggestions (points to 'suggest' method).
Route::get('/api/search-suggest', [DiscoverController::class, 'suggest'])->name('api.search.suggest');

// 4. ADDED: A new route for your changeCountry method.
Route::post('/change-country', [DiscoverController::class, 'changeCountry'])->name('discover.changeCountry');


// Standard auth routes
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';