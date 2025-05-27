<?php

namespace App\Events;

use App\Models\AssignedEmail;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailReplied
{
    use Dispatchable, SerializesModels;

    public $email;
    public $replyContent;

    public function __construct(AssignedEmail $email, string $replyContent)
    {
        $this->email = $email;
        $this->replyContent = $replyContent;
    }
}
