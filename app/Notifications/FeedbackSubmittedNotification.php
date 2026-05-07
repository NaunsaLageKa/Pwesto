<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FeedbackSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'status' => 'pending',
            'title' => 'Feedback submitted',
            'message' => 'Your feedback is submitted.',
            'url' => route('profile.feedback'),
        ];
    }
}
