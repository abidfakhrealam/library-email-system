<?php

namespace App\Services\Email;

use App\Models\AssignedEmail;
use Carbon\Carbon;

class ImapService
{
    private $connection;

    public function connect(): void
    {
        $this->connection = imap_open(
            "{outlook.office365.com:993/imap/ssl}INBOX",
            config('mail.username'),
            config('mail.password')
        );

        if (!$this->connection) {
            throw new \Exception("IMAP connection failed: " . imap_last_error());
        }
    }

    public function fetchEmails(int $limit = 50): array
    {
        $this->connect();
        $newEmails = [];

        $emails = imap_search($this->connection, 'ALL', SE_UID);
        $emails = array_slice($emails, 0, $limit);

        foreach ($emails as $emailUid) {
            $header = imap_headerinfo($this->connection, imap_msgno($this->connection, $emailUid));
            
            $existing = AssignedEmail::where('message_id', $emailUid)->first();
            if (!$existing) {
                $newEmails[] = AssignedEmail::create([
                    'message_id' => $emailUid,
                    'subject' => $header->subject,
                    'body_preview' => $this->getBodyPreview($emailUid),
                    'from_email' => $header->from[0]->mailbox . '@' . $header->from[0]->host,
                    'from_name' => $header->from[0]->personal ?? '',
                    'received_at' => Carbon::parse($header->date),
                    'status' => 'unassigned'
                ]);
            }
        }

        imap_close($this->connection);
        return $newEmails;
    }

    private function getBodyPreview($emailUid): string
    {
        $body = imap_fetchbody($this->connection, $emailUid, 1);
        return mb_substr(strip_tags($body), 0, 200);
    }
}