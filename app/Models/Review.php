<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function hubOwnerDismissals()
    {
        return $this->hasMany(HubOwnerReviewDismissal::class);
    }

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
        'published_to_public_at',
        'published_to_public_by',
        'admin_archived_at',
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
        'admin_archived_at' => 'datetime',
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

    public function publishedToPublicBy()
    {
        return $this->belongsTo(User::class, 'published_to_public_by');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Whether this review should appear (or be actionable) for the given hub owner account.
     */
    public function isOwnedByHubOwner(int $hubOwnerId): bool
    {
        if ((int) $this->hub_owner_id === $hubOwnerId) {
            return true;
        }

        $hubOwner = User::find($hubOwnerId);
        if (! $hubOwner || $this->feedback_type !== 'workspace') {
            return false;
        }

        if ($this->hubOwner && trim((string) $hubOwner->company) !== '') {
            $a = strtolower(trim((string) $hubOwner->company));
            $b = strtolower(trim((string) ($this->hubOwner->company ?? '')));
            if ($b !== '') {
                if ($a === $b) {
                    return true;
                }
                if (strlen($a) >= 3 && strlen($b) >= 3 && (str_contains($a, $b) || str_contains($b, $a))) {
                    return true;
                }
            }
        }

        if (! $this->booking || ! $hubOwner->company) {
            return false;
        }

        $booking = $this->booking;
        if ((int) $booking->hub_owner_id === $hubOwnerId) {
            return true;
        }

        $hubName = strtolower((string) ($booking->hub_name ?? ''));
        $company = strtolower(trim((string) $hubOwner->company));

        return $hubName !== '' && $company !== '' && str_contains($hubName, $company);
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
     * Scope for approved reviews visible to hub owner (workspace feedback for their hub).
     * Includes rows stored with this hub_owner_id, or workspace feedback tied to a booking
     * for this hub (fixes legacy rows where hub_owner_id pointed at another matched account).
     * Also includes workspace rows stored against another hub_owner user when both share the
     * same workspace company branding (resolveHubOwnerForWorkspace may not pick the same row
     * as the account used to log into the hub dashboard).
     */
    public function scopeApprovedForHubOwner($query, $hubOwnerId)
    {
        $hubOwner = User::find($hubOwnerId);

        return $query->where('status', 'approved')
            ->where(function ($q) use ($hubOwnerId, $hubOwner) {
                $q->where('hub_owner_id', $hubOwnerId);

                if ($hubOwner && trim((string) $hubOwner->company) !== '') {
                    $companyLike = '%' . strtolower($hubOwner->company) . '%';
                    $companyNorm = strtolower(trim($hubOwner->company));

                    $q->orWhere(function ($inner) use ($hubOwnerId, $companyLike) {
                        $inner->where('feedback_type', 'workspace')
                            ->whereHas('booking', function ($bq) use ($hubOwnerId, $companyLike) {
                                $bq->where(function ($bqq) use ($hubOwnerId, $companyLike) {
                                    $bqq->where('hub_owner_id', $hubOwnerId)
                                        ->orWhereRaw('LOWER(hub_name) LIKE ?', [$companyLike]);
                                });
                            });
                    });

                    $q->orWhere(function ($inner) use ($companyNorm) {
                        $inner->where('feedback_type', 'workspace')
                            ->whereIn('hub_owner_id', User::query()
                                ->where('role', 'hub_owner')
                                ->where(function ($uq) use ($companyNorm) {
                                    $uq->whereRaw('LOWER(TRIM(company)) = ?', [$companyNorm])
                                        ->orWhere(function ($uq2) use ($companyNorm) {
                                            $uq2->whereRaw('LENGTH(TRIM(company)) >= 3', [])
                                                ->whereRaw('? LIKE CONCAT("%", LOWER(TRIM(company)), "%")', [$companyNorm]);
                                        });
                                    if (strlen($companyNorm) >= 3) {
                                        $uq->orWhereRaw('LOWER(TRIM(company)) LIKE CONCAT("%", ?, "%")', [$companyNorm]);
                                    }
                                })
                                ->select('id'));
                    });
                }
            })
            ->orderBy('created_at', 'desc');
    }
}
