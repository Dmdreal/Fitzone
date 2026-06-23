<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymEquipment extends Model
{
    protected $table = 'gym_equipment';

    protected $fillable = [
        'name',
        'category',
        'serial_number',
        'status',
        'last_serviced_at',
        'next_service_at',
    ];

    protected $casts = [
        'last_serviced_at' => 'date',
        'next_service_at' => 'date',
    ];
}
