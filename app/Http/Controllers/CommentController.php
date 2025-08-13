<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Criar comentário.
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('create', Comment::class);

        $request->validate(['content' => 'required|string']);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Atualizar comentário.
     * - O $comment agora é injetado diretamente pela rota.
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate(['content' => 'required|string']);

        $comment->update(['content' => $request->input('content')]);

        return response()->json($comment);
    }

    /**
     * Responder comentário.
     */
    public function reply(Request $request, $commentId)
    {
        $this->authorize('create', Comment::class);

        $parent = Comment::findOrFail($commentId);
        $data = $request->validate(['content' => 'required|string']);

        $reply = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $parent->post_id,
            'content' => $data['content'],
            'parent_id' => $parent->id,
        ]);

        $reply->load('user');

        return response()->json($reply, 201);
    }
    
    /**
     * Deletar comentário.
     */
    public function destroy(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->route('community.page', ['id' => $post->community->id])
            ->with('success', 'Comentário excluído com sucesso!');
    }

    /**
     * Listar thread de comentários.
     */
    public function showThread(Post $post)
    {
        $comments = $post->comments()->with('replies.user')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }
}
