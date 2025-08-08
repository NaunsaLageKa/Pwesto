<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hub_owner_id',
        'booking_id',
        'type',
        'description',
        'evidence',
        'status',
        'resolution',
        'resolved_by',
        'resolved_at',
        'escalated_at',
        'created_by'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'escalated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hubOwner()
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
