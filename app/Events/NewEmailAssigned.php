<?php

namespace App\Events;

use App\Models\AssignedEmail;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEmailAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    public function __construct(AssignedEmail $email)
    {
        $this->email = $email;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->email->assigned_to);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->email->id,
            'subject' => $this->email->subject,
            'from' => $this->email->from_name,
            'received_at' => $this->email->received_at->diffForHumans(),
            'url' => route('emails.show', $this->email)
        ];
    }
}
