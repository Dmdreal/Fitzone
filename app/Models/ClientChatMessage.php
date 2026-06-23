<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewChatMessage;

class ClientChatMessage extends Model
{
    protected $fillable = [
        'client_chat_id',
        'sender_id',
        'body',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(ClientChat::class, 'client_chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    protected static function booted(): void
    {
        static::created(function (ClientChatMessage $message) {
            $chat = $message->chat()->first();
            if (! $chat) {
                return;
            }

            // determine recipient based on chat type and sender
            $recipient = null;
            if ($chat->type === 'trainer_direct') {
                if ($message->sender_id === $chat->member_id) {
                    $recipient = $chat->trainer;
                } else {
                    $recipient = $chat->member;
                }
            } else {
                // fallback: notify trainer if present, otherwise member
                $recipient = $chat->trainer ?? $chat->member;
            }

            if ($recipient) {
                $recipient->notify(new NewChatMessage($message));
            }
        });
    }
}
