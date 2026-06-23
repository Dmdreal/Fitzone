<?php

namespace App\Models;

use App\Models\MembershipPackage;
use Illuminate\Database\Eloquent\Model;

class TrainerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialty',
        'category',
        'rating',
        'experience_years',
        'bio',
        'photo_path',
        'preferred_package_id',
        'preferred_rate',
        'county_id',
        'town',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'preferred_rate' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    public function preferredPackage()
    {
        return $this->belongsTo(MembershipPackage::class, 'preferred_package_id');
    }
}
