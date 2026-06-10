<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallRequest extends Model
{
    protected $fillable = [
        'caller_id',
        'trainer_id',
        'membership_id',
        'client_chat_id',
        'status',
        'offer_sdp',
        'answer_sdp',
        'caller_ice',
        'trainer_ice',
        'accepted_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'caller_ice' => 'array',
            'trainer_ice' => 'array',
            'accepted_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function chat()
    {
        return $this->belongsTo(ClientChat::class, 'client_chat_id');
    }
}
