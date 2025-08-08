<?php

use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Landing page routes
Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');
Route::get('/about', [LandingPageController::class, 'about'])->name('landing.about');
Route::get('/features', [LandingPageController::class, 'features'])->name('landing.features');
Route::get('/contact', [LandingPageController::class, 'contact'])->name('landing.contact');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/myprofile', [ProfileController::class, 'myprofile'])->name('profile.myprofile');
    Route::get('/myprofileedit', [ProfileController::class, 'myprofileedit'])->name('profile.myprofileedit');
    Route::post('/myprofilestore', [ProfileController::class, 'myprofilestore'])->name('profile.myprofilestore');
    Route::get('/myappointment', [ProfileController::class, 'myappointment'])->name('profile.myappointment');
});

require __DIR__.'/auth.php';
