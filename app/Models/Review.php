<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hub_owner_id',
        'booking_id',
        'rating',
        'comment',
        'feedback_type',
        'status',
        'priority',
        'is_flagged',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejected_by',
        'moderation_notes',
        'hub_owner_response',
        'hub_owner_responded_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'priority' => 'integer',
        'is_flagged' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'hub_owner_responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hubOwner()
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    
    public function scopePendingPriority($query)
    {
        return $query->where('status', 'pending')
                    ->orderByRaw('GREATEST(COALESCE(priority, 0), CASE WHEN rating <= 2 THEN 1 ELSE 0 END) DESC')
                    ->orderBy('is_flagged', 'desc')
                    ->orderBy('created_at', 'asc');
    }

    /**
     * Whether this review should be treated as high priority for moderation.
     
     */
    public function isHighPriority(): bool
    {
        return $this->priority == 1 || $this->rating <= 2;
    }

    /**
     * Scope for approved reviews visible to hub owner
     */
    public function scopeApprovedForHubOwner($query, $hubOwnerId)
    {
        return $query->where('status', 'approved')
                    ->where('hub_owner_id', $hubOwnerId)
                    ->orderBy('created_at', 'desc');
    }
}
