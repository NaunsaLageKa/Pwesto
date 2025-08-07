<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class HubOwnerStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;

    public function __construct(User $owner)
    {
        $this->owner = $owner;
    }

    public function build()
    {
        $status = $this->owner->status;
        $subject = $status === 'approved' ? 'Your Hub Owner Registration is Approved' : 'Your Hub Owner Registration is Rejected';
        return $this->subject($subject)
            ->view('emails.hub-owner-status-changed');
    }
} 