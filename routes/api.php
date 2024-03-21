<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admin\Users\AdminUserController;
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

    // Admin routes
    Route::group(['prefix' => 'admin', 'middleware' => [AdminMiddleware::class]], function () {

        //Admin users routes
        Route::group(['prefix' => 'users'], function () {
            Route::get('/users', [AdminUserController::class, 'paginated']);
            Route::get('/users/{user_id}', [AdminUserController::class, 'show']);
            Route::put('/users/{user_id}', [AdminUserController::class, 'update']);
            Route::delete('/users/{user_id}', [AdminUserController::class, 'delete']);
        });
    });

    Route::post('logout', [AuthController::class, 'logout']);
});
