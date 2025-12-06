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
        'moderation_notes'
    ];

    protected $casts = [
        'rating' => 'integer',
        'priority' => 'integer',
        'is_flagged' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
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

    /**
     * Scope for pending reviews ordered by priority
     */
    public function scopePendingPriority($query)
    {
        return $query->where('status', 'pending')
                    ->orderBy('priority', 'desc')
                    ->orderBy('is_flagged', 'desc')
                    ->orderBy('created_at', 'asc');
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
