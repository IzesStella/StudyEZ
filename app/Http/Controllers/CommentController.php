<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // 1. Monitorando comenta em um post
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $validated['post_id'],
            'content' => $validated['content'],
            'parent_id' => null, // comentário raiz
        ]);

        return response()->json($comment, 201);
    }

    // 2. Criar subcomentário (resposta a um comentário)
    public function reply(Request $request, $commentId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $parentComment = Comment::findOrFail($commentId);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $reply = Comment::create([
            'user_id' => $user->id,
            'post_id' => $parentComment->post_id,
            'content' => $validated['content'],
            'parent_id' => $parentComment->id,
        ]);

        return response()->json($reply, 201);
    }

    // 3. Deletar comentário (opcional)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Você não tem permissão para deletar esse comentário.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentário deletado com sucesso.'], 200);
    }

    // 4. Retorna comentários com subcomentários organizados em thread
    public function showThread($postId)
    {
        $post = Post::findOrFail($postId);

        // Busca comentários raiz do post com suas respostas (subcomentários)
        $comments = Comment::with('replies.user')
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }
}