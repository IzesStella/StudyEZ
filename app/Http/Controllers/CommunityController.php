<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommunityController extends Controller
{
    public function __construct()
    {
        // exige auth em tudo, menos explore/search/page
        $this->middleware('auth')->except(['explore', 'search', 'page']);
    }

    /**
     * Cria nova comunidade (só monitor)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Community::class);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $data['user_id'] = $request->user()->id;
        Community::create($data);

        return back()->with('success', 'Comunidade criada com sucesso!');
    }

    /**
     * Aluno se inscreve na comunidade (só student)
     */
    public function join($id)
    {
        $community = Community::findOrFail($id);
        $this->authorize('join', $community);

        $user = auth()->user();
        if ($community->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Você já participa dessa comunidade.');
        }

        $community->users()->attach($user->id);
        return back()->with('success', 'Você entrou na comunidade com sucesso.');
    }

    /**
     * Aluno sai da comunidade (só student)
     */
    public function leave($id)
    {
        $community = Community::findOrFail($id);
        $this->authorize('join', $community);

        $user = auth()->user();
        if (! $community->users()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Você não participa dessa comunidade.');
        }

        $community->users()->detach($user->id);
        return back()->with('success', 'Você saiu da comunidade com sucesso.');
    }

    /**
     * Atualiza comunidade (só monitor dono)
     */
    public function update(Request $request, $id)
    {
        $community = Community::findOrFail($id);
        $this->authorize('update', $community);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $community->update($data);
        return response()->json($community);
    }

    /**
     * Remove comunidade (só monitor dono)
     */
    public function destroy($id)
    {
        $community = Community::findOrFail($id);
        $this->authorize('delete', $community);

        $community->delete();
        return response()->json(null, 204);
    }

    /**
     * API JSON: lista todas as comunidades
     */
    public function index()
    {
        return response()->json(Community::all());
    }

    /**
     * API JSON: detalhes de uma comunidade
     */
    public function show($id)
    {
        return response()->json(
            Community::with('users', 'creator')->findOrFail($id)
        );
    }

    /**
     * Página com todas as comunidades (Inertia)
     */
    public function explore()
    {
        $communities = Community::all();
        return Inertia::render('CommunityPage', ['communities' => $communities]);
    }

    /**
     * Busca comunidades por nome (Inertia)
     */
    public function search(Request $request)
    {
        $q = $request->input('search', '');
        $communities = Community::with('creator')
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->get();

        return Inertia::render('Search', ['communities' => $communities, 'search' => $q]);
    }

    /**
     * Página de comunidade específica (Inertia)
     */
    public function page($id)
    {
        $community = Community::with('creator', 'users')->findOrFail($id);
        return Inertia::render('CommunityPage', ['community' => $community]);
    }
}
