<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        // obriga estar logado em todas as ações deste controller
        $this->middleware('auth');
    }

    /**
     * Listar todos os posts de uma comunidade (público).
     */
    public function index($communityId)
    {
        $community = Community::findOrFail($communityId);
        $posts = $community->posts()->with('user')->latest()->get();

        return response()->json($posts);
    }

    /**
     * Mostrar um post específico (público).
     */
    public function show($id)
    {
        $post = Post::with(['user', 'community'])->findOrFail($id);
        return response()->json($post);
    }

    /**
     * Criar novo post:
     * - Alunos podem em qualquer comunidade.
     * - Monitores apenas nas próprias.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'nullable|string',
            'image_path'   => 'nullable|url',
            'community_id' => 'required|exists:communities,id',
        ]);

        $community = Community::findOrFail($data['community_id']);
        // Policy::create → recebe (User, Community)
        $this->authorize('create', [$community]);

        $post = new Post($data);
        $post->user_id      = $request->user()->id;
        $post->community_id = $community->id;
        $post->save();

        return response()->json($post, 201);
    }

    /**
     * Atualizar post (só monitor dono da comunidade).
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $data = $request->validate([
            'title'      => 'sometimes|string|max:255',
            'content'    => 'nullable|string',
            'image_path' => 'nullable|url',
        ]);

        $post->update($data);

        return response()->json($post);
    }

    /**
     * Deletar post (só monitor dono da comunidade).
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();
        return response()->json(['message' => 'Post deletado com sucesso.'], 200);
    }
}
