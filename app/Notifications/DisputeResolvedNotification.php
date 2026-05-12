<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;


class DisputeResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Dispute $dispute,
        /** 'reporter' (the person who filed) or 'reported' (the other party) */
        public string $recipientRole,
        /** One of: warning, suspension, ban, refund, no_action */
        public string $action,
        /** Optional admin resolution text for context */
        public ?string $resolution = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->buildTitle();
        $message = $this->buildMessage();

        // Hub owners land on their dashboard; customers land on booking history.
        $isHubOwner = ($notifiable->role ?? null) === 'hub_owner';
        $url = $isHubOwner
            ? route('hub-owner.dashboard')
            : route('booking-history');

        return [
            'dispute_id' => $this->dispute->id,
            'booking_id' => $this->dispute->booking_id,
            'action' => $this->action,
            'status' => 'resolved',
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ];
    }

    private function buildTitle(): string
    {
        if ($this->recipientRole === 'reporter') {
            return 'Your dispute report was resolved';
        }

        return match ($this->action) {
            'warning' => 'Warning issued',
            'suspension' => 'Account suspended',
            'ban' => 'Account banned',
            'refund' => 'Refund approved',
            'no_action' => 'Dispute closed',
            default => 'Dispute resolved',
        };
    }

    private function buildMessage(): string
    {
        $resolutionSuffix = $this->resolution
            ? ' Resolution: ' . $this->resolution
            : '';

        if ($this->recipientRole === 'reporter') {
            $actionSummary = match ($this->action) {
                'warning' => 'A warning was issued to the other party.',
                'suspension' => 'The other party\'s account has been suspended.',
                'ban' => 'The other party\'s account has been banned.',
                'refund' => 'A refund has been approved.',
                'no_action' => 'No action was taken after review.',
                default => 'The dispute has been resolved.',
            };

            return 'An admin reviewed your dispute report. ' . $actionSummary . $resolutionSuffix;
        }

        return match ($this->action) {
            'warning' => 'An admin has issued you a warning regarding a recent booking dispute.' . $resolutionSuffix,
            'suspension' => 'Your account has been suspended due to a booking dispute.' . $resolutionSuffix,
            'ban' => 'Your account has been banned due to a booking dispute.' . $resolutionSuffix,
            'refund' => 'A refund has been approved on a disputed booking.' . $resolutionSuffix,
            'no_action' => 'A dispute filed regarding one of your bookings has been reviewed and closed. No action was taken.',
            default => 'A dispute regarding one of your bookings has been resolved.' . $resolutionSuffix,
        };
    }
}
