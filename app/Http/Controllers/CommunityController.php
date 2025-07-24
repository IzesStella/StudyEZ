<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * Armazena uma nova comunidade (somente para monitores).
     */
    public function store(Request $request)
    {
        // Validação dos campos
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Atribui o usuário autenticado como criador
        $data['user_id'] = $request->user()->id;

        // Cria a comunidade no banco
        Community::create($data);

        // Retorna para a página anterior (Inertia capturará o sucesso)
        return back()->with('success', 'Comunidade criada com sucesso!');
    }

    /**
     * Adiciona o monitor logado a uma comunidade.
     */
    public function join($id)
    {
        $community = Community::findOrFail($id);
        $user = auth()->user();

        if ($community->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Você já participa dessa comunidade.');
        }

        $community->users()->attach($user->id);

        return back()->with('success', 'Você entrou na comunidade com sucesso.');
    }

    /**
     * Remove o monitor logado de uma comunidade.
     */
    public function leave($id)
    {
        $community = Community::findOrFail($id);
        $user = auth()->user();

        if (! $community->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Você não participa dessa comunidade.');
        }

        $community->users()->detach($user->id);

        return back()->with('success', 'Você saiu da comunidade com sucesso.');
    }

    /**
     * Lista todas as comunidades (se precisar de API JSON).
     */
    public function index()
    {
        return response()->json(Community::all());
    }

    /**
     * Exibe os detalhes de uma única comunidade.
     */
    public function show($id)
    {
        return response()->json(
            Community::with('users', 'creator')->findOrFail($id)
        );
    }

    /**
     * Atualiza os dados de uma comunidade.
     */
    public function update(Request $request, $id)
    {
        $community = Community::findOrFail($id);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $community->update($data);

        return response()->json($community);
    }

    /**
     * Remove uma comunidade.
     */
    public function destroy($id)
    {
        $community = Community::findOrFail($id);
        $community->delete();

        return response()->json(null, 204);
    }

}
