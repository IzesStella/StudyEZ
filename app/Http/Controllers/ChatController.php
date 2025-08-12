<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Chat;
use App\Models\Message;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Inertia\Response as InertiaResponse;

class ChatController extends Controller
{
    /**
     * Display a listing of the chats for the authenticated user.
     *
     * @return \Inertia\Response
     */
    public function index(): InertiaResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        // Obtém os chats do usuário com as relações necessárias
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'messages' => fn($q) => $q->latest()->limit(1),
            'users' => fn($q) => $q->where('id', '!=', $user->id),
        ])
        ->latest('updated_at') // Ordena pelo chat mais recente
        ->get();

        $formattedChats = $chats->map(function ($chat) {
            $otherUser = $chat->users->first();
            $lastMessage = $chat->messages->first();

            return [
                'id' => $chat->id,
                'user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'role' => $otherUser->role,
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

    /**
     * Display the specified chat.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Inertia\Response
     */
    public function show(Chat $chat): InertiaResponse
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        // Verifica se o usuário logado pertence a este chat
        if (!$chat->users()->where('id', $user->id)->exists()) {
            abort(403, 'Acesso negado.');
        }

        // Carrega mensagens e usuários relacionados
        $messages = $chat->messages()->with('user')->get();

        // Obtém os chats do usuário, assim como no método index
        $chats = Chat::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'messages' => fn($q) => $q->latest()->limit(1),
            'users' => fn($q) => $q->where('id', '!=', $user->id),
        ])
        ->latest('updated_at')
        ->get();
        
        $formattedChats = $chats->map(function ($c) {
            $otherUser = $c->users->first();
            $lastMessage = $c->messages->first();

            return [
                'id' => $c->id,
                'user' => $otherUser ? [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'role' => $otherUser->role,
                ] : null,
                'lastMessage' => $lastMessage ? $lastMessage->content : null,
            ];
        });

        // Formata o chat atual
        $otherUser = $chat->users()->where('id', '!=', $user->id)->first();
        $formattedCurrentChat = [
            'id' => $chat->id,
            'user' => $otherUser ? [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'role' => $otherUser->role ?? null,
            ] : null,
            'name' => $otherUser ? "Chat com {$otherUser->name}" : "Chat #{$chat->id}"
        ];

        return Inertia::render('ChatPage', [
            'chats' => $formattedChats,
            'currentChat' => $formattedCurrentChat,
            'messages' => $messages,
        ]);
    }

    /**
     * Store a newly created chat message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
            'content' => 'required|string',
        ]);

        $chat = Chat::find($request->input('chat_id'));
        $userId = auth()->id();

        if (!$chat || !$chat->users()->where('id', $userId)->exists()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'user_id' => $userId,
            'content' => $request->input('content'),
        ]);

        // Atualiza o timestamp do chat
        $chat->touch();

        broadcast(new NewChatMessage($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Mensagem enviada!',
            'data' => $message->load('user')
        ]);
    }
/**
 * Rota para iniciar ou abrir um chat com um monitor.
 *
 * @param  \App\Models\User  $monitor
 * @return \Illuminate\Http\RedirectResponse
 */
public function startChat(User $monitor): RedirectResponse
{
    $user = auth()->user();
    if (!$user) {
        abort(403);
    }

    // Procura um chat existente entre o usuário e o monitor
    $chat = Chat::whereHas('users', function ($query) use ($user) {
        $query->where('user_id', $user->id);
    })->whereHas('users', function ($query) use ($monitor) {
        $query->where('user_id', $monitor->id);
    })->first();

    // Se não existir, cria um novo chat
    if (!$chat) {
        DB::transaction(function () use (&$chat, $user, $monitor) {
            $chat = Chat::create(['name' => "Chat com {$monitor->name}"]);
            $chat->users()->attach([$user->id, $monitor->id]);
        });
    }

    // Redireciona para a rota show do chat
    return redirect()->route('chat.show', ['chat' => $chat->id]);
}
}