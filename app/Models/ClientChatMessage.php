<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientChatMessage extends Model
{
    protected $fillable = [
        'client_chat_id',
        'sender_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function chat()
    {
        return $this->belongsTo(ClientChat::class, 'client_chat_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
