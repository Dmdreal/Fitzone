<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPackage extends Model
{
    public const HIDDEN_SLUGS = ['trial-prompt'];

    protected $fillable = [
        'name',
        'slug',
        'duration_unit',
        'duration_count',
        'price',
        'access_level',
        'trainer_access',
        'benefits',
        'is_active',
    ];

    protected $casts = [
        'benefits' => 'array',
        'trainer_access' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function scopeVisible($query)
    {
        return $query
            ->where('is_active', true)
            ->whereNotIn('slug', self::HIDDEN_SLUGS);
    }

    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class);
    }
}
