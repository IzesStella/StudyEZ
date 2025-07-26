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
     * Criar comentário (só aluno).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Comment::class);

        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string',
        ]);

        $comment = Comment::create([
            'user_id'   => $request->user()->id,
            'post_id'   => $data['post_id'],
            'content'   => $data['content'],
            'parent_id' => null,
        ]);

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
