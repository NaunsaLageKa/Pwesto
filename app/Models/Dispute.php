<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
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

    /**
     * Short title for legacy DB columns and admin lists (type + truncated description).
     */
    /**
     * Who filed this report (customer vs hub owner), for admin display.
     */
    public function reporterRoleLabel(): ?string
    {
        if (! $this->created_by) {
            return null;
        }

        if ((int) $this->created_by === (int) $this->user_id) {
            return 'customer';
        }

        if ((int) $this->created_by === (int) $this->hub_owner_id) {
            return 'hub_owner';
        }

        return $this->createdBy?->role;
    }

    public function reporter(): ?User
    {
        if ($this->relationLoaded('createdBy') && $this->createdBy) {
            return $this->createdBy;
        }

        $role = $this->reporterRoleLabel();
        if ($role === 'customer') {
            return $this->user;
        }
        if ($role === 'hub_owner') {
            return $this->hubOwner;
        }

        return $this->createdBy;
    }

    public function reportedAgainst(): ?User
    {
        $role = $this->reporterRoleLabel();
        if ($role === 'customer') {
            return $this->hubOwner;
        }
        if ($role === 'hub_owner') {
            return $this->user;
        }

        return null;
    }

    public function hubDisplayName(): string
    {
        if ($this->relationLoaded('booking') && $this->booking) {
            $hubName = trim((string) ($this->booking->hub_name ?? ''));
            if ($hubName !== '') {
                return $hubName;
            }
        } elseif ($this->booking_id) {
            $hubName = trim((string) ($this->booking()->value('hub_name') ?? ''));
            if ($hubName !== '') {
                return $hubName;
            }
        }

        $company = trim((string) ($this->hubOwner?->company ?? ''));
        if ($company !== '') {
            return $company;
        }

        return $this->hubOwner?->name ?? 'Unknown workspace';
    }

    public function customerDisplayName(): string
    {
        return $this->user?->name ?? 'Unknown user';
    }

    public function reporterByRoleLabel(): string
    {
        return match ($this->reporterRoleLabel()) {
            'customer' => 'Customer',
            'hub_owner' => 'Hub owner',
            default => 'Reporter',
        };
    }

    public function reporterByDisplayName(): string
    {
        if ($this->reporterRoleLabel() === 'hub_owner') {
            return $this->hubDisplayName();
        }

        return $this->reporter()?->name ?? 'Unknown';
    }

    public function reporterEmail(): ?string
    {
        return $this->reporter()?->email;
    }

    public function reportedAgainstRoleLabel(): string
    {
        return match ($this->reporterRoleLabel()) {
            'customer' => 'Workspace',
            'hub_owner' => 'User',
            default => 'Unknown',
        };
    }

    public function reportedAgainstDisplayName(): string
    {
        $role = $this->reporterRoleLabel();
        if ($role === 'customer') {
            return $this->hubDisplayName();
        }
        if ($role === 'hub_owner') {
            return $this->customerDisplayName();
        }

        return 'Unknown';
    }

    /** Email of the reported party — only when a hub owner reports a user (not workspace reports). */
    public function reportedPartyEmail(): ?string
    {
        if ($this->reporterRoleLabel() === 'hub_owner') {
            return $this->user?->email;
        }

        return null;
    }

    public static function summaryTitle(string $type, string $description): string
    {
        $labels = [
            'payment' => 'Payment issue',
            'service' => 'Property / workspace issue',
            'behavior' => 'Behavior issue',
            'other' => 'Other issue',
        ];
        $head = $labels[$type] ?? ucfirst($type);
        $tail = Str::limit(trim(preg_replace('/\s+/', ' ', $description)), 200);

        return Str::limit("{$head} — {$tail}", 255);
    }
}
