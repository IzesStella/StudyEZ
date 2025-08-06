<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Criar comentário (só aluno ou monitor).
     * O post_id agora é recebido da URL.
     */
    public function store(Request $request, $postId)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id'   => $request->user()->id,
            'post_id'   => $postId,
            'content'   => $data['content'],
            'parent_id' => null,
        ]);

        // Carrega o relacionamento 'user' no comentário recém-criado
        $comment->load('user'); // <-- ADICIONADO: Carrega os dados do usuário

        return response()->json($comment, 201);
    }

    /**
     * Responder comentário (só aluno).
     */
    public function reply(Request $request, $commentId)
    {
        $this->authorize('create', Comment::class);

        $parent = Comment::findOrFail($commentId);
        $data = $request->validate(['content' => 'required|string']);

        $reply = Comment::create([
            'user_id'   => $request->user()->id,
            'post_id'   => $parent->post_id,
            'content'   => $data['content'],
            'parent_id' => $parent->id,
        ]);

        // Carrega o relacionamento 'user' na resposta recém-criada
        $reply->load('user'); // <-- ADICIONADO: Carrega os dados do usuário

        return response()->json($reply, 201);
    }

    /**
     * Deletar comentário (só autor aluno).
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('delete', $comment);

        $comment->delete();
        return response()->json(['message' => 'Comentário deletado com sucesso.'], 200);
    }

    /**
     * Listar thread de comentários (público).
     */
    public function showThread($postId)
    {
        $comments = Comment::with('replies.user')
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }
}