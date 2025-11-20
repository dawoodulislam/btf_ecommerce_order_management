<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\ProductController;
use App\Http\Controllers\API\v1\ProductVariantController;

Route::prefix('v1')->name('api.v1.')->group(function(){
    Route::post('auth/register', [AuthController::class,'register']);
    Route::post('auth/login', [AuthController::class,'login']);
    Route::post('auth/refresh', [AuthController::class,'refresh']);

    // Public product listing
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Protected product operations
    Route::middleware(['jwt', 'role:admin,vendor'])->group(function () {

        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::post('products/import', [ProductController::class,'import']);

        // Variant operations
        Route::post('/products/{id}/variants', [ProductVariantController::class, 'store']);
        Route::put('/variants/{variantId}', [ProductVariantController::class, 'update']);
        Route::delete('/variants/{variantId}', [ProductVariantController::class, 'destroy']);
    });

    Route::middleware(['jwt'])->group(function () {

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice']);

        // restricted to admin/vendor only
        Route::middleware(['role:admin,vendor'])->group(function () {
            Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        });
    });
});