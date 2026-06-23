<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'client_id',
        'target_user_id',
        'target_type',
        'subject',
        'body',
        'status',
    ];
}
