<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Membership extends Model
{
    protected $fillable = [
        'member_id',
        'membership_package_id',
        'trainer_id',
        'gym_owner_id',
        'starts_at',
        'ends_at',
        'status',
        'activated_at',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'activated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function package()
    {
        return $this->belongsTo(MembershipPackage::class, 'membership_package_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function gymOwner()
    {
        return $this->belongsTo(User::class, 'gym_owner_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopePaidActive(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->whereDate('ends_at', '>=', now()->toDateString())
            ->whereHas('payments', fn (Builder $query) => $query->where('status', 'paid'));
    }

    public static function expireEndedMemberships(): int
    {
        return static::query()
            ->where('status', 'active')
            ->whereDate('ends_at', '<', now()->toDateString())
            ->update(['status' => 'expired']);
    }
}
