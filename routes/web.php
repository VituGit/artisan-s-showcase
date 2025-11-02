<?php

use App\Http\Controllers\AtelierController;
use App\Http\Controllers\AtelierLpConfigController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicAtelierController;
use App\Http\Controllers\Api\LpContactController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/lp', [PublicAtelierController::class, 'lpPage'])->name('lp.page');
Route::post('/lp/contact', [LpContactController::class, 'store'])->name('lp.contact');

// Public routes
Route::get('ateliers/{atelier}', [PublicAtelierController::class, 'show'])->name('public.ateliers.show');
Route::get('ateliers/{atelier}/products/{product}', [PublicAtelierController::class, 'showProduct'])->name('public.products.show');

Route::middleware(['auth', 'verified'])->group(function () {
    // Atelier creation (without middleware check)
    Route::get('ateliers/create', [AtelierController::class, 'create'])->name('ateliers.create');
    Route::post('ateliers', [AtelierController::class, 'store'])->name('ateliers.store');

    // Protected routes - require atelier
    Route::middleware(['atelier.required'])->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        // Atelier management routes
        Route::get('ateliers', [AtelierController::class, 'index'])->name('ateliers.index');
        Route::get('ateliers/{atelier}', [AtelierController::class, 'show'])->name('ateliers.show');
        Route::get('ateliers/{atelier}/edit', [AtelierController::class, 'edit'])->name('ateliers.edit');
        Route::put('ateliers/{atelier}', [AtelierController::class, 'update'])->name('ateliers.update');
        Route::delete('ateliers/{atelier}', [AtelierController::class, 'destroy'])->name('ateliers.destroy');

        // Products routes
        Route::resource('products', ProductController::class);
        Route::post('products/{product}/photos', [ProductController::class, 'uploadPhotos'])->name('products.photos.upload');
        Route::delete('products/{product}/photos/{photo}', [ProductController::class, 'deletePhoto'])->name('products.photos.delete');
        Route::post('products/{product}/photos/{photo}/cover', [ProductController::class, 'setCover'])->name('products.photos.cover');
        Route::post('products/{product}/photos/reorder', [ProductController::class, 'reorderPhotos'])->name('products.photos.reorder');

        // Categories routes
        Route::resource('categories', CategoryController::class);

        // LP Config routes
        Route::get('lp-config', [AtelierLpConfigController::class, 'edit'])->name('lp-config.edit');
        Route::put('lp-config', [AtelierLpConfigController::class, 'update'])->name('lp-config.update');
        Route::post('lp-config/draft', [AtelierLpConfigController::class, 'saveDraft'])->name('lp-config.draft');
        Route::post('lp-config/upload-image', [AtelierLpConfigController::class, 'uploadImage'])->name('lp-config.upload-image');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Public LP route - MUST BE LAST to not catch other routes
Route::get('/{slug}', [PublicAtelierController::class, 'show'])->name('public.atelier');
