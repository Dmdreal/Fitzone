<?php

namespace App\Notifications;

use App\Models\ClientChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChatMessage extends Notification
{
    use Queueable;

    private ClientChatMessage $message;

    public function __construct(ClientChatMessage $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'chat_id' => $this->message->client_chat_id,
            'sender_id' => $this->message->sender_id,
            'body' => $this->message->body,
        ];
    }
}
