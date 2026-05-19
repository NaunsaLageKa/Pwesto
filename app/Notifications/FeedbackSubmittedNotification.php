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
        $isWorkspace = $this->review->feedback_type === 'workspace';

        return [
            'review_id' => $this->review->id,
            'status' => $isWorkspace ? 'approved' : 'pending',
            'title' => 'Feedback submitted',
            'message' => $isWorkspace
                ? 'Your workspace feedback was shared with the venue and added to public reviews.'
                : 'Your platform feedback is pending admin review.',
            'url' => route('profile.feedback'),
        ];
    }
}
