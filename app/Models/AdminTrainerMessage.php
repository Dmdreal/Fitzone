<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTrainerMessage extends Model
{
    protected $fillable = [
        'admin_id',
        'trainer_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
}
