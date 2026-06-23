<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\MembershipPackage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'county_id',
        'role',
        'status',
        'member_number',
        'qr_token',
        'profile_photo_path',
        'phone',
        'headline',
        'bio',
        'location',
        'nearby_locations',
        'age',
        'gender',
        'fitness_goal',
        'experience_level',
        'budget_range',
        'diet_preference',
        'gym_name',
        'gym_services',
        'verification_status',
        'preferred_package_id',
        'preferred_rate',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->ensureMemberIdentity();
        });
    }

    public function trainerProfile()
    {
        return $this->hasOne(TrainerProfile::class);
    }

    public function preferredPackage()
    {
        return $this->belongsTo(MembershipPackage::class, 'preferred_package_id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class, 'member_id');
    }

    public function assignedMemberships()
    {
        return $this->hasMany(Membership::class, 'trainer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_id');
    }

    public function workoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'member_id');
    }

    public function assignedWorkoutPlans()
    {
        return $this->hasMany(WorkoutPlan::class, 'trainer_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'member_id');
    }

    public function dietPlans()
    {
        return $this->hasMany(DietPlan::class, 'member_id');
    }

    public function progressRecords()
    {
        return $this->hasMany(ProgressRecord::class, 'member_id');
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class, 'member_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'member_id');
    }

    public function cafeOrders()
    {
        return $this->hasMany(Order::class, 'member_id');
    }

    public function sentChatMessages()
    {
        return $this->hasMany(ClientChatMessage::class, 'sender_id');
    }

    public function trainerAdminMessages()
    {
        return $this->hasMany(AdminTrainerMessage::class, 'trainer_id');
    }

    public function sentTrainerAdminMessages()
    {
        return $this->hasMany(AdminTrainerMessage::class, 'admin_id');
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/'.$this->profile_photo_path)
            : null;
    }

    public function ensureMemberIdentity(): void
    {
        if ($this->role !== 'member') {
            return;
        }

        $changes = [];

        if (! $this->member_number) {
            $changes['member_number'] = 'GYM-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
        }

        if (! $this->qr_token) {
            $changes['qr_token'] = bin2hex(random_bytes(16));
        }

        if ($changes !== []) {
            $this->forceFill($changes)->save();
        }
    }
}
