<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'member_id',
        'membership_id',
        'trainer_id',
        'gym_owner_id',
        'phone',
        'receipt',
        'amount',
        'method',
        'status',
        'reference',
        'transaction_code',
        'paid_at',
        'notes',
        'mpesa_checkout_request_id',
        'mpesa_merchant_request_id',
        'mpesa_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'mpesa_response' => 'array',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function gymOwner()
    {
        return $this->belongsTo(User::class, 'gym_owner_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
