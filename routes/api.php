<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admin\Users\AdminUserController;
use App\Http\Controllers\Admin\Products\AdminProductCategoryController;
use App\Http\Controllers\Admin\Products\AdminProductController;
use App\Http\Controllers\Admin\Products\AdminProductPriceListController;
use App\Http\Controllers\Products\ProductCategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Middleware\AdminMiddleware;

// Health check route
Route::get('_health', function() {
    return 'I am healthy!';
});

// Auth routes 
Route::group(['prefix' => 'auth'], function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group([
    'middleware' => ['auth:sanctum']
], function() {
    
    // User routes
    Route::group(['prefix' => 'users'], function() {
        Route::get('', [UserController::class, 'show']);
        Route::put('', [UserController::class, 'update']);
        Route::delete('', [UserController::class, 'delete']);
    });

     // Product category routes
     Route::group(['prefix' => 'product-categories'], function() {
        Route::get('', [ProductCategoryController::class, 'paginated']);

        Route::group(['prefix' => '{product_category_id}'], function() {
            Route::get('', [ProductCategoryController::class, 'show']);
            Route::get('products', [ProductCategoryController::class, 'products']);
        });
    });

     // Product routes
     Route::group(['prefix' => 'products'], function() {
        Route::get('', [ProductController::class, 'paginated']);
        Route::get('{product_id}', [ProductController::class, 'show']);
    });

    // Admin routes
    Route::group(['prefix' => 'admin', 'middleware' => [AdminMiddleware::class]], function () {

        //Admin users routes
        Route::group(['prefix' => 'users'], function () {
            Route::get('', [AdminUserController::class, 'paginated']);
            Route::get('{user_id}', [AdminUserController::class, 'show']);
            Route::put('{user_id}', [AdminUserController::class, 'update']);
            Route::delete('{user_id}', [AdminUserController::class, 'delete']);
        });

        //Admin product category routes
         Route::group(['prefix' => 'product-categories'], function () {
            Route::post('', [AdminProductCategoryController::class, 'create']);
            Route::get('', [AdminProductCategoryController::class, 'paginated']);
            Route::get('{product_category_id}', [AdminProductCategoryController::class, 'show']);
            Route::put('{product_category_id}', [AdminProductCategoryController::class, 'update']);
            Route::delete('{product_category_id}', [AdminProductCategoryController::class, 'delete']);
        });

        //Admin product routes
        Route::group(['prefix' => 'products'], function () {
            Route::post('', [AdminProductController::class, 'create']);
            Route::get('', [AdminProductController::class, 'paginated']);
            Route::get('{product_id}', [AdminProductController::class, 'show']);
            Route::put('{product_id}', [AdminProductController::class, 'update']);
            Route::delete('{product_id}', [AdminProductController::class, 'delete']);
        });

        //Admin product price list routes
         Route::group(['prefix' => 'product-price-lists'], function () {
            Route::post('', [AdminProductPriceListController::class, 'create']);
            Route::get('', [AdminProductPriceListController::class, 'paginated']);
            Route::get('{product_price_list_id}', [AdminProductPriceListController::class, 'show']);
            Route::put('{product_price_list_id}', [AdminProductPriceListController::class, 'update']);
            Route::delete('{product_price_list_id}', [AdminProductPriceListController::class, 'delete']);
        });
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
