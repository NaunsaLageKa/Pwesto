<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'phone',
        'role', // Added role
        'status', // Added status
        'company', // Added
        'company_id', // Added
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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the bookings for the user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the bookings where this user is the hub owner.
     */
    public function hubBookings()
    {
        return $this->hasMany(Booking::class, 'hub_owner_id');
    }

    /**
     * Get reviews submitted by this user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get reviews for this user's workspace (if hub owner).
     */
    public function hubReviews()
    {
        return $this->hasMany(Review::class, 'hub_owner_id');
    }

    /**
     * Hub bans issued by this workspace (hub owner).
     */
    public function hubUserBansIssued(): HasMany
    {
        return $this->hasMany(HubUserBan::class, 'hub_owner_id');
    }

    public function isBannedFromHubOwner(int $hubOwnerId): bool
    {
        return HubUserBan::where('hub_owner_id', $hubOwnerId)
            ->where('user_id', $this->id)
            ->exists();
    }
}
