<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['member_id', 'balance'];

    protected $casts = ['balance' => 'decimal:2'];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
