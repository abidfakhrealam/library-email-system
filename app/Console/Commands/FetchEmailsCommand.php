<?php

namespace App\Console\Commands;

use App\Services\Email\GraphApiService;
use Illuminate\Console\Command;
use App\Models\User;

class FetchEmailsCommand extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Fetch new emails from all configured mailboxes';

    public function handle()
    {
        $mailboxes = [
            'library-help@university.edu',
            'library-requests@university.edu',
            'library-support@university.edu'
        ];

        $user = User::where('is_admin', true)->first();
        if (!$user) {
            $this->error('No admin user found to fetch emails');
            return 1;
        }

        $service = new GraphApiService($user->access_token);
        
        $count = 0;
        foreach ($mailboxes as $mailbox) {
            $newEmails = $service->fetchEmails($mailbox);
            $count += count($newEmails);
        }

        $this->info("Fetched {$count} new emails");
        return 0;
    }
}