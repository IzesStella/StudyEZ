<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Listar todos os posts de uma comunidade
    public function index($communityId)
    {
        $community = Community::findOrFail($communityId);
        $posts = $community->posts()->with('user')->latest()->get();

        return response()->json($posts);
    }

    // Criar um novo post (por monitor)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|url',
            'community_id' => 'required|exists:communities,id',
        ]);

        // Para fins acadêmicos, usuário fixo
        $user = User::find(2);

        $post = new Post($validated);
        $post->user_id = $user->id;
        $post->save();

        return response()->json($post, 201);
    }

    // Mostrar um post específico
    public function show($id)
    {
        $post = Post::with(['user', 'community'])->findOrFail($id);

        return response()->json($post);
    }

    // Atualizar um post (somente se for o autor)
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        if ($post->user_id !== $user->id) {
            return response()->json(['message' => 'Você não tem permissão para editar esse post.'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|url',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    // Deletar um post (somente se for o autor)
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        if ($post->user_id !== $user->id) {
            return response()->json(['message' => 'Você não tem permissão para deletar esse post.'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deletado com sucesso.'], 200);
    }
}
