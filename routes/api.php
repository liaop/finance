<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\LedgerDetailController;


Route::post('/', function () {
    return response()->json(['message' => 'Welcome to the API']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 以下路由需要认证
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('ledgers', LedgerController::class);
    Route::apiResource('ledgers.details', LedgerDetailController::class);
});
