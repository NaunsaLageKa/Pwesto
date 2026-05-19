<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubUserBan extends Model
{
    protected $fillable = [
        'hub_owner_id',
        'user_id',
    ];

    public function hubOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hub_owner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
