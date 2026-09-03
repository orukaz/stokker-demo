<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFavoriteController;
use App\Http\Controllers\SavedFilterController;
use App\Http\Controllers\SourceCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => to_route('products.index'))->name('home');
Route::inertia('/welcome', 'Welcome')->name('welcome');
Route::inertia('/it-tiimi-uus-tookorraldus', 'docs/ItWorkOrganization')
    ->name('docs.it_work_organization');
Route::get('/source-code/{sourceSet}', [SourceCodeController::class, 'show'])
    ->middleware('cache.headers:public;max_age=3600;etag')
    ->name('source_code.show');
Route::inertia('/demos/dev-193/phone-field', 'demos/PhoneField', [
    'siteCountryIsos' => config('phone.site_country_isos'),
])
    ->name('demos.dev_193.phone_field');
Route::prefix('demos/dev-238')->name('demos.dev_238.')->group(function () {
    Route::get('/saved-filters', [SavedFilterController::class, 'index'])
        ->name('saved_filters.index');
    Route::post('/saved-filters', [SavedFilterController::class, 'store'])
        ->name('saved_filters.store');
    Route::put('/saved-filters/reorder', [SavedFilterController::class, 'reorder'])
        ->name('saved_filters.reorder');
    Route::patch('/saved-filters/{savedFilter}', [SavedFilterController::class, 'update'])
        ->name('saved_filters.update');
    Route::put('/saved-filters/{savedFilter}/default', [SavedFilterController::class, 'makeDefault'])
        ->name('saved_filters.make_default');
    Route::delete('/saved-filters/{savedFilter}', [SavedFilterController::class, 'destroy'])
        ->name('saved_filters.destroy');
});
Route::prefix('demos/dev-160')->name('demos.dev_160.')->group(function () {
    Route::get('/discount-reason-modal', fn () => response()->file(
        public_path('dev-160-discount-reason-modal.html'),
    ))->name('discount_reason_modal');
    Route::get('/discount-reason-dropdown', fn () => response()->file(
        public_path('dev-160-discount-reason-dropdown.html'),
    ))->name('discount_reason_dropdown');
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
