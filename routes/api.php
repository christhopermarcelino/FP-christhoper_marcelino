<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth-jwt', 'api'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/orders/report', [OrderController::class, 'report']);
    Route::resource('/orders', OrderController::class);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::resource('/categories', CategoryController::class);
Route::resource('/products', ProductController::class);
