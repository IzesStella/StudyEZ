<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Rota raiz para pré‑login, apenas para convidados
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Prelogin');
    })->name('prelogin');

    // Registro
    Route::get('/register', [RegisteredUserController::class, 'create'])
         ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// ** ESTA PARTE GARANTE QUE /dashboard EXISTA **
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))
         ->name('dashboard');

    // Dashboard do monitor
    Route::get('/dashboard-monitor', fn() => Inertia::render('DashboardMonitor'))
         ->name('dashboard.monitor');

    // Dashboard do aluno (monitorando)
    Route::get('/dashboard-aluno', fn() => Inertia::render('DashboardAluno'))
         ->name('dashboard.aluno');

     // Rota de pesquisa 
    Route::get('/search', fn() => Inertia::render('Search'))
         ->name('search');

    // Rota de perfil
    Route::get('/profile', fn() => Inertia::render('Profile'))
         ->name('profile');

    // logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');
});

// Rotas de reset de senha e verificação de e‑mail
require __DIR__.'/auth.php';

