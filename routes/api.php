<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\AuthController;


Route::apiResource('users', UserController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Suas rotas protegidas, exemplo:
    Route::apiResource('communities', CommunityController::class);
    Route::post('communities/{id}/join', [CommunityController::class, 'join']);
    Route::post('communities/{id}/leave', [CommunityController::class, 'leave']);
});