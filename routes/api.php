<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

Route::apiResource('users', UserController::class);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Comunidades
    Route::apiResource('communities', CommunityController::class);
    Route::post('communities/{id}/join', [CommunityController::class, 'join']);
    Route::post('communities/{id}/leave', [CommunityController::class, 'leave']);

    // Posts
    Route::get('/communities/{communityId}/posts', [PostController::class, 'index']); // listar posts por comunidade
    Route::apiResource('posts', PostController::class)->except(['index']);

    // Comentários
    Route::post('/comments', [CommentController::class, 'store']); // criar comentário
    Route::post('/comments/{commentId}/reply', [CommentController::class, 'reply']); // responder comentário
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); // deletar comentário
    Route::get('/posts/{postId}/comments/thread', [CommentController::class, 'showThread']); // mostrar thread de comentários
});
