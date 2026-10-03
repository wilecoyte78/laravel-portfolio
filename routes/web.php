<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatabaseViewerController;
use App\Http\Controllers\Admin\GmailController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ResumeController as AdminResumeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;

// ------------------------------------------------------------------
// Public site
// ------------------------------------------------------------------
Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::redirect('/home', '/', 301);

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/resume', [ResumeController::class, 'show'])->name('resume');

// ------------------------------------------------------------------
// Admin area (auth + is_admin required)
// ------------------------------------------------------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('pages', PageController::class)->except(['show']);

    Route::get('navigation', [NavigationController::class, 'index'])->name('navigation.index');
    Route::post('navigation', [NavigationController::class, 'store'])->name('navigation.store');
    Route::put('navigation/{navigationItem}', [NavigationController::class, 'update'])->name('navigation.update');
    Route::post('navigation/reorder', [NavigationController::class, 'reorder'])->name('navigation.reorder');
    Route::delete('navigation/{navigationItem}', [NavigationController::class, 'destroy'])->name('navigation.destroy');

    // Read-only database browser (GET only, by design)
    Route::get('database', [DatabaseViewerController::class, 'index'])->name('database.index');
    Route::get('database/{table}', [DatabaseViewerController::class, 'show'])->name('database.show');

    Route::get('gmail/connect', [GmailController::class, 'connect'])->name('gmail.connect');
    Route::get('gmail/callback', [GmailController::class, 'callback'])->name('gmail.callback');

    Route::get('resume', [AdminResumeController::class, 'index'])->name('resume.index');
    Route::post('resume', [AdminResumeController::class, 'update'])->name('resume.update');
});

require __DIR__.'/auth.php';

// ------------------------------------------------------------------
// Catch-all dynamic page route — keep this LAST.
// ------------------------------------------------------------------
Route::get('/{slug}', [PublicPageController::class, 'show'])
    ->where('slug', '^(?!(?:login|logout|register|dashboard|admin|forgot-password|reset-password|two-factor-challenge|email|user|settings)(?:/|$))[a-z0-9\-\/]+$')
    ->name('page.show');
