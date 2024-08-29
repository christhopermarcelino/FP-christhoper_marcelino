<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth-jwt', 'api'])->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', function() {
        return response()->json(auth()->user(), 200);
    });
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);