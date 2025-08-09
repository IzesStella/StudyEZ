<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class ChatController extends Controller
{
    public function show($monitorId = null)
    {
        // Aqui, você buscaria a lista real de conversas do usuário logado.
        // Por enquanto, vamos usar dados estáticos para a lista.
        $chats = [
            [
                'id' => 1,
                'user' => ['id' => 3, 'name' => 'Pedro Henrique'],
                'lastMessage' => 'Oi, tudo bem?',
            ],
            [
                'id' => 2,
                'user' => ['id' => 4, 'name' => 'Mariana Neves'],
                'lastMessage' => 'Sim, obrigada!',
            ],
        ];

        $currentChat = null;
        $messages = [];

        // Se um monitorId foi passado, encontramos o chat correspondente
        if ($monitorId) {
            $monitor = User::find($monitorId);
            
            // Aqui, você faria a lógica para carregar as mensagens
            // entre o usuário logado e o monitor.
            $messages = [
                // Mensagens de exemplo
                ['content' => 'Oi, tudo bem?', 'sender_id' => $monitor->id],
                ['content' => 'Obrigado!', 'sender_id' => auth()->id()],
            ];

            $currentChat = [
                'id' => $monitorId,
                'user' => ['id' => $monitor->id, 'name' => $monitor->name],
            ];
        }

        return Inertia::render('ChatPage', [
            'chats' => $chats,
            'currentChat' => $currentChat, // O chat que deve estar aberto
            'messages' => $messages,
        ]);
    }
}