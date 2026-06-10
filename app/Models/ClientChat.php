<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientChat extends Model
{
    protected $fillable = [
        'type',
        'title',
        'membership_package_id',
        'member_id',
        'trainer_id',
    ];

    public function package()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function messages()
    {
        return $this->hasMany(ClientChatMessage::class)->latest();
    }
}
