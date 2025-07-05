<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1) Validação
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', Rule::in(['student', 'monitor'])],
        ]);

        // 2) Cria o usuário — hash aplicado apenas aqui
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // 3) Dispara o evento Registered (para e‑mail de confirmação opcional)
        event(new Registered($user));

        // 4) Redireciona para login (sem login automático)
        return redirect()
               ->route('login')
               ->with('success', 'Conta criada com sucesso! Faça login para continuar.');
    }
}
