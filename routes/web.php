<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFavoriteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('products.index'))->name('home');
Route::inertia('/welcome', 'Welcome')->name('welcome');
Route::prefix('demos/dev-160')->name('demos.dev_160.')->group(function () {
    Route::get('/discount-reason-modal', fn () => response()->file(
        public_path('dev-160-discount-reason-modal.html'),
    ))->name('discount_reason_modal');
    Route::get('/discount-report', fn () => response()->file(
        public_path('dev-160-discount-report.html'),
    ))->name('discount_report');
});

Route::get('/et/mootorsaed', [ProductController::class, 'index'])->name('products.index');
Route::post('/products/{product}/favorite', [ProductFavoriteController::class, 'store'])->name('products.favorite.store');
Route::delete('/products/{product}/favorite', [ProductFavoriteController::class, 'destroy'])->name('products.favorite.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
