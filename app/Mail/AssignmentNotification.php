<?php

namespace App\Mail;

use App\Models\AssignedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AssignmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    public function __construct(AssignedEmail $email)
    {
        $this->email = $email;
    }

    public function build()
    {
        return $this->markdown('emails.assignment-notification')
            ->subject('New Email Assignment: ' . $this->email->subject)
            ->with([
                'email' => $this->email,
                'url' => route('emails.show', $this->email)
            ]);
    }
}
