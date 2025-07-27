<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas para convidados (guest)
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => Inertia::render('Prelogin'))->name('prelogin');

    Route::get('/register', [RegisteredUserController::class, 'create'])
         ->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
         ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
         ->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
         ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
         ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
         ->name('password.store');
});

// Rotas para usuários autenticados (auth)
Route::middleware('auth')->group(function () {
    //
    // 1) Rota genérica /dashboard redireciona conforme papel do usuário
    //
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return $user->role === 'monitor'
            ? redirect()->route('dashboard.monitor')
            : redirect()->route('dashboard.aluno');
    })->name('dashboard');

    //
    // 2) Rotas diretas para cada painel
    //
    Route::get('/dashboard-aluno', [UserController::class, 'dashboard'])
         ->name('dashboard.aluno');
    Route::get('/dashboard-monitor', fn() => Inertia::render('DashboardMonitor'))
         ->name('dashboard.monitor');

    //
    // 3) Comunidades: buscar, explorar, ver, criar (monitor)
    //
    Route::get('/search',      [CommunityController::class, 'search'])
         ->name('search');
    Route::get('/communities', [CommunityController::class, 'explore'])
         ->name('communities.explore');
    Route::get('/community/{id}', [CommunityController::class, 'page'])
         ->name('community.page');
    Route::post('/communities', [CommunityController::class, 'store'])
         ->name('communities.store');

    //
    // 4) Inscrição e cancelamento (aluno apenas)
    //
    Route::post('/communities/{id}/subscribe',   [CommunityController::class, 'subscribe'])
         ->name('communities.subscribe');
    Route::delete('/communities/{id}/unsubscribe',[CommunityController::class, 'unsubscribe'])
         ->name('communities.unsubscribe');

    //
    // 5) Perfil e logout
    //
    Route::get('/profile',         fn() => Inertia::render('Profile'))
         ->name('profile.show');
    Route::get('/profile/edit',    [ProfileController::class, 'edit'])
         ->name('profile.edit');
    Route::patch('/profile',       [ProfileController::class, 'update'])
         ->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
         ->name('profile.avatar');
    Route::delete('/profile',      [ProfileController::class, 'destroy'])
         ->name('profile.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
         ->name('logout');
});

// Password reset etc. (Breeze)
require __DIR__.'/auth.php';
