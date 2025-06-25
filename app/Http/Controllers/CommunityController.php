<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    // Listar todas as comunidades
    public function index()
    {
        $communities = Community::all();
        return response()->json($communities);
    }

    // Criar uma nova comunidade (somente para monitores)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Associa a comunidade ao usuário logado
        $community = new Community($validated);
        $community->user_id = 2; // monitor dono da comunidade
        $community->save();

        return response()->json($community, 201);
    }

    // Mostrar detalhes de uma comunidade
    public function show($id)
    {
        $community = Community::with('users')->findOrFail($id);
        return response()->json($community);
    }

    // Atualizar dados da comunidade
    public function update(Request $request, $id)
    {
        $community = Community::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
        ]);

        $community->update($validated);

        return response()->json($community);
    }

    // Deletar uma comunidade
    public function destroy($id)
    {
        $community = Community::findOrFail($id);
        $community->delete();

        return response()->json(null, 204);
    }

    // Monitorando entra em uma comunidade
    public function join($id)
    {
        $community = Community::findOrFail($id);
        $user = Auth::user(2);

        if ($community->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Você já participa dessa comunidade.'], 409);
        }

        $community->users()->attach($user->id);

        return response()->json(['message' => 'Entrou na comunidade com sucesso.'], 200);
    }

    // Monitorando sai da comunidade
    public function leave($id)
    {
        $community = Community::findOrFail($id);
        $user = Auth::user(2);

        if (!$community->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Você não participa dessa comunidade.'], 404);
        }

        $community->users()->detach($user->id);

        return response()->json(['message' => 'Saiu da comunidade com sucesso.'], 200);
    }
}
