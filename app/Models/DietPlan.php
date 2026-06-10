<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DietPlan extends Model
{
    protected $fillable = [
        'member_id',
        'membership_package_id',
        'name',
        'goal',
        'daily_calories',
        'meal_schedule',
        'meal_delivery_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meal_schedule' => 'array',
            'meal_delivery_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function package()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id');
    }
}
