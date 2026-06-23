<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['member_id', 'handled_by', 'order_number', 'status', 'total_amount', 'paid_at'];

        protected $casts = [
            'items' => 'array',
            'notes' => 'array',
            'created_at' => 'datetime',
        ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
