<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        // Já carrega o usuário junto para evitar consultas extras no front
        $this->message = $message->load('user');
    }

    /**
     * Define o canal como público.
     */
    public function broadcastOn()
    {
        // Troque 'PrivateChannel' por 'Channel'
        return new Channel('chat.' . $this->message->chat_id);
    }

    /**
     * Define o nome do evento no WebSocket.
     */
    public function broadcastAs()
    {
        return 'NewChatMessage';
    }

    /**
     * Define o payload que o front vai receber.
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'content' => $this->message->content,
            'user' => [
                'id' => $this->message->user->id,
                'name' => $this->message->user->name
            ],
            'created_at' => $this->message->created_at->toDateTimeString()
        ];
    }
}