<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GmailController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

// ------------------------------------------------------------------
// Public site
// ------------------------------------------------------------------
Route::get('/', [PublicPageController::class, 'home'])->name('home');

Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

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

    Route::get('gmail/connect', [GmailController::class, 'connect'])->name('gmail.connect');
    Route::get('gmail/callback', [GmailController::class, 'callback'])->name('gmail.callback');
});

require __DIR__.'/auth.php';

// ------------------------------------------------------------------
// Catch-all dynamic page route — keep this LAST.
// ------------------------------------------------------------------
Route::get('/{slug}', [PublicPageController::class, 'show'])
    ->where('slug', '[a-z0-9\-\/]+')
    ->name('page.show');
