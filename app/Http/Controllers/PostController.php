<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Community;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Listar todos os posts de uma comunidade.
     * Corrigido para usar Route Model Binding na comunidade.
     */
    public function index(Community $community)
    {
        $posts = $community->posts()->with('user')->latest()->get();
        return response()->json($posts);
    }

    /**
     * Mostrar um post específico.
     * Rota show não é aninhada, então não precisa do parâmetro Community.
     */
    public function show($id)
    {
        $post = Post::with(['user', 'community'])->findOrFail($id);
        return response()->json($post);
    }

    /**
     * Criar novo post.
     * Corrigido para receber o community_id do corpo da requisição.
     * Isso evita o erro de 'community_id cannot be null' quando o Route Model Binding falha.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|url',
            // Adicionado community_id para validação
            'community_id' => 'required|exists:communities,id'
        ]);
        
        $community = Community::findOrFail($data['community_id']);
        
        $this->authorize('create', [Post::class, $community]);

        $post = new Post($data);
        $post->user_id = $request->user()->id;
        $post->save();

        return redirect()->route('community.page', $community->id)
                         ->with('success', 'Post criado com sucesso!');
    }

    /**
     * Atualizar post.
     * Esta função já estava correta, recebendo ambos os modelos.
     */
    public function update(Request $request, Community $community, Post $post)
    {
        // O Laravel já injeta as instâncias de Community e Post
        // com base nos IDs da URL da rota aninhada.
        
        $this->authorize('update', $post);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'image_path' => 'nullable|url',
        ]);

        $post->update($data);

        return redirect()->route('community.page', $community->id)
                         ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Deletar post.
     * Corrigido para usar Route Model Binding em Community e Post.
     * A rota DELETE /communities/{community}/posts/{post} espera ambos os modelos.
     */
    public function destroy(Community $community, Post $post)
    {
        // O Laravel já injeta as instâncias de Community e Post.
        // A comunidade é o primeiro parâmetro, o post é o segundo.
        
        $this->authorize('delete', $post);

        $post->delete();
        
        // Redireciona usando a instância da comunidade injetada.
        return redirect()->route('community.page', $community->id)
                         ->with('success', 'Post deletado com sucesso!');
    }
}
