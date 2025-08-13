<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlannerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
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
    // ➤  rotas do Planner
    //
    Route::get('/planner', [PlannerController::class, 'show'])
        ->name('planner.show');
    Route::post('/planner', [PlannerController::class, 'store'])
        ->name('planner.store');

    //
    // 3) Comunidades: buscar, explorar, ver, criar (monitor)
    //
    Route::get('/search', [CommunityController::class, 'search'])
        ->name('search');
    Route::get('/communities', [CommunityController::class, 'explore'])
        ->name('communities.explore');
    Route::get('/my-communities', [CommunityController::class, 'myCommunitiesApi'])
        ->name('communities.mine');
    Route::get('/community/{id}', [CommunityController::class, 'page'])
        ->name('community.page');
    Route::post('/communities', [CommunityController::class, 'store'])
        ->name('communities.store');

    // 4) Rotas de CRUD de Comunidades (Adicionadas para o erro anterior)
    Route::put('/communities/{id}', [CommunityController::class, 'update'])->name('communities.update');
    Route::delete('/communities/{id}', [CommunityController::class, 'destroy'])->name('communities.destroy');
    
    //
    // ➤ Rotas do Chat
    //
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{chat}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.store');
    // Rota para iniciar ou abrir um chat com um monitor
    Route::post('/chat/start/{monitor}', [ChatController::class, 'startChat'])
        ->name('chat.start');
    
    //
    // 5) Inscrição e cancelamento (aluno apenas)
    //
    Route::post('/communities/{id}/subscribe', [CommunityController::class, 'subscribe'])
        ->name('communities.subscribe');
    Route::delete('/communities/{id}/unsubscribe',[CommunityController::class, 'unsubscribe'])
        ->name('communities.unsubscribe');
    
    //
    // ➤ Rotas de CRUD de Posts e Comentários (reorganizado e corrigido)
    //
    // As rotas de recurso aninhadas já geram as URLs corretas para criar,
    // ler, atualizar e deletar. O erro estava na chamada do front-end.
    Route::resource('communities.posts', PostController::class)->except(['create', 'edit']);
    Route::resource('posts.comments', CommentController::class)->except(['create', 'edit']);
    
    //
    // 6) Perfil e logout
    //
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::get('/profile/{user}', [ProfileController::class, 'showUser'])
        ->name('profile.user');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Password reset etc. (Breeze)
require __DIR__.'/auth.php';