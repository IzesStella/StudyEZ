<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommunityController;

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

    // tela de busca
    Route::get('/search', fn() => Inertia::render('Search'))
         ->name('search');

     //rota pra buscar comunidades
     Route::get('/communities', [CommunityController::class, 'explore'])
     ->middleware('auth')
     ->name('communities.explore');


    // Perfil
    Route::get('/profile', fn() => Inertia::render('Profile'))->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');

       // ROTA IMPORTANTE PARA BUSCAR COMUNIDADES DO MONITOR LOGADO
         // Essa rota retorna as comunidades em JSON (ex: para o dashboard)
         // Usada em DashboardMonitor.vue via Axios, após criar uma comunidade
      Route::get('/communities', [CommunityController::class, 'index'])
         ->name('communities.index');
     //comunidade - criação
     Route::post('/communities', [CommunityController::class, 'store'])
     ->name('communities.store');

});


// Rota de verificação de e‑mail e confirmação de senha (Breeze)
require __DIR__.'/auth.php';
