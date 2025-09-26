<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hub_owner_id',
        'hub_name',
        'service_type',
        'seat_id',
        'seat_label',
        'booking_date',
        'booking_time',
        'start_time',
        'end_time',
        'status',
        'amount',
        'notes',
        'floor_plan_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hubOwner()
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }

    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class);
    }
}
