<?php

namespace App\Notifications;

use App\Models\AssignedEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;

class EmailAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AssignedEmail $email)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Email Assignment: {$this->email->subject}")
            ->line("You have been assigned a new email:")
            ->line("From: {$this->email->from_name} <{$this->email->from_email}>")
            ->line("Subject: {$this->email->subject}")
            ->action('View Email', route('emails.show', $this->email))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'email_id' => $this->email->id,
            'subject' => $this->email->subject,
            'from' => $this->email->from_name,
            'message' => 'You have been assigned a new email',
            'url' => route('emails.show', $this->email)
        ];
    }
}