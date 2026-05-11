<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * In-app notification fired when an admin escalates a dispute to senior review.
 *
 * Sent to both parties (reporter + reported) so they know the case is still
 * being investigated and is not closed.
 */
class DisputeEscalatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Dispute $dispute,
        /** 'reporter' or 'reported' */
        public string $recipientRole
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isHubOwner = ($notifiable->role ?? null) === 'hub_owner';
        $url = $isHubOwner
            ? route('hub-owner.dashboard')
            : route('booking-history');

        $title = $this->recipientRole === 'reporter'
            ? 'Your dispute was escalated'
            : 'Dispute escalated for senior review';

        $message = $this->recipientRole === 'reporter'
            ? 'An admin has escalated your dispute report to senior management for further review.'
            : 'A dispute regarding one of your bookings has been escalated to senior management for further review.';

        return [
            'dispute_id' => $this->dispute->id,
            'booking_id' => $this->dispute->booking_id,
            'status' => 'escalated',
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ];
    }
}
