<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = [
        'member_id',
        'points',
        'source',
        'description',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
