<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        // Obtém os chats usando o DB para evitar o erro de método indefinido
        $chats = DB::table('chats')
            ->join('chat_user', 'chats.id', '=', 'chat_user.chat_id')
            ->where('chat_user.user_id', $user->id)
            ->select('chats.id')
            ->pluck('chats.id');

        $chatList = Chat::whereIn('id', $chats)
            ->with([
                'messages' => fn($q) => $q->latest()->limit(1),
                'users' => fn($q) => $q->where('id', '!=', $user->id),
            ])->get();

        $formattedChats = $chatList->map(function ($chat) {
            $otherUser = $chat->users->first();
            $lastMessage = $chat->messages->first();

            return [
                'id' => $chat->id,
                'user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'role' => $otherUser->role, // Adicione a função aqui
                ] : null,
                'lastMessage' => $lastMessage ? $lastMessage->content : null,
            ];
        });

        return Inertia::render('ChatPage', [
            'chats' => $formattedChats,
            'currentChat' => null,
            'messages' => [],
        ]);
    }

    public function show(Chat $chat)
    {
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        if (! $chat->users()->where('id', $user->id)->exists()) {
            abort(403);
        }

        // Obtém os chats usando o DB para evitar o erro de método indefinido
        $chats = DB::table('chats')
            ->join('chat_user', 'chats.id', '=', 'chat_user.chat_id')
            ->where('chat_user.user_id', $user->id)
            ->select('chats.id')
            ->pluck('chats.id');

        $chatList = Chat::whereIn('id', $chats)
            ->with([
                'messages' => fn($q) => $q->latest()->limit(1),
                'users' => fn($q) => $q->where('id', '!=', $user->id),
            ])->get();
            
        $formattedChats = $chatList->map(function ($c) {
            $otherUser = $c->users->first();
            $lastMessage = $c->messages->first();

            return [
                'id' => $c->id,
                'user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'role' => $otherUser->role, // Adicione a função aqui
                ] : null,
                'lastMessage' => $lastMessage ? $lastMessage->content : null,
            ];
        });

        $messages = $chat->messages()->with('user')->get();

        $otherUser = $chat->users()->where('id', '!=', $user->id)->first();
        $formattedCurrentChat = [
            'id' => $chat->id,
            'user' => $otherUser ? [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'role' => $otherUser->role ?? null, // Aqui também para a página de chat
            ] : null,
            'name' => $otherUser ? "Chat com {$otherUser->name}" : "Chat #{$chat->id}"
        ];

        return Inertia::render('ChatPage', [
            'chats' => $formattedChats,
            'currentChat' => $formattedCurrentChat,
            'messages' => $messages,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required|string',
        ]);

        $chat = Chat::find($request->input('chat_id'));
        $userId = auth()->id();

        if (! $chat || ! $chat->users()->where('id', $userId)->exists()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'content' => $request->input('content'),
        ]);

        // Dispara evento para WebSocket puro
        broadcast(new NewChatMessage($message))->toOthers();

        // Retorne um redirecionamento ou uma resposta vazia para o Inertia
        // Retornar nada ou um objeto vazio para o Inertia.
        return back()->with('success', 'Mensagem enviada!');
    }

    public function startChat(User $monitor)
    {
        $aluno = auth()->user();
        if (! $aluno) {
            abort(403);
        }

        if ($monitor->id === $aluno->id) {
            return redirect()->back();
        }

        $chat = Chat::whereHas('users', fn($q) => $q->where('id', $aluno->id))
            ->whereHas('users', fn($q) => $q->where('id', $monitor->id))
            ->first();

        if (! $chat) {
            DB::transaction(function () use (&$chat, $aluno, $monitor) {
                $chat = Chat::create(['name' => "Chat com {$monitor->name}"]);
                $chat->users()->attach([$aluno->id, $monitor->id]);
            });
        }

        return redirect()->route('chat.show', ['chat' => $chat->id]);
    }
}
