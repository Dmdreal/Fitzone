<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'member_id',
        'membership_id',
        'phone',
        'receipt',
        'amount',
        'method',
        'status',
        'reference',
        'paid_at',
        'notes',
        'mpesa_checkout_request_id',
        'mpesa_merchant_request_id',
        'mpesa_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'mpesa_response' => 'array',
        ];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
