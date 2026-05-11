<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        /** One of: confirmed, cancelled, rejected */
        public string $eventStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $hub = $this->booking->hub_name ?? 'your workspace';
        $date = $this->booking->booking_date?->format('M j, Y') ?? '';

        $title = match ($this->eventStatus) {
            'paid' => 'Booking paid',
            'confirmed' => 'Booking approved',
            'cancelled' => 'Booking cancelled',
            'rejected' => 'Booking declined',
            default => 'Booking update',
        };

        $message = match ($this->eventStatus) {
            'paid' => "Payment received for your booking at {$hub}".($date ? " on {$date}" : '').'.',
            'confirmed' => "Your booking at {$hub}".($date ? " on {$date}" : '').' has been approved.',
            'cancelled' => "Your booking at {$hub}".($date ? " on {$date}" : '').' has been cancelled.',
            'rejected' => "Your booking at {$hub}".($date ? " on {$date}" : '').' was declined by the hub.',
            default => 'Your booking status was updated.',
        };

        return [
            'booking_id' => $this->booking->id,
            'status' => $this->eventStatus,
            'title' => $title,
            'message' => $message,
            'transaction_number' => $this->eventStatus === 'paid'
                ? $this->booking->transaction_number
                : null,
            'url' => route('booking-history'),
        ];
    }
}
