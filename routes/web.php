<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas de convidados (guest)
Route::middleware('guest')->group(function () {
    // Pré‑login
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

    // Esqueci a senha (Breeze)
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
         ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
         ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
         ->name('password.store');
});

// Rotas autenticadas (auth)
Route::middleware('auth')->group(function () {
    // Dashboard genérico que dispatcha para os dois componentes
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role === 'monitor') {
            return Inertia::render('DashboardMonitor');
        }

        // padrão = aluno
        return Inertia::render('DashboardAluno');
    })->name('dashboard');

    // (Opcional) rotas separadas para acessos diretos
    Route::get('/dashboard-monitor', fn() => Inertia::render('DashboardMonitor'))
         ->name('dashboard.monitor');
    Route::get('/dashboard-aluno',   fn() => Inertia::render('DashboardAluno'))
         ->name('dashboard.aluno');

    // Busca
    Route::get('/search', fn() => Inertia::render('Search'))
         ->name('search');

    // Perfil
    Route::get('/profile', fn() => Inertia::render('Profile'))
         ->name('profile');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');
});

// Rota de verificação de e‑mail e confirmação de senha (Breeze)
require __DIR__.'/auth.php';
