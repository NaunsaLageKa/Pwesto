<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'hub_owner_id',
        'name',
        'layout_data',
        'description',
        'is_active',
    ];

    protected $casts = [
        'layout_data' => 'array',
        'is_active' => 'boolean',
    ];

    public function hubOwner()
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }
}
