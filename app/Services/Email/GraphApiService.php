<?php

namespace App\Services\Email;

use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\Models\AssignedEmail;
use Carbon\Carbon;

class GraphApiService
{
    private $graph;
    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->graph = new Graph();
        $this->accessToken = $accessToken;
        $this->graph->setAccessToken($this->accessToken);
    }

    public function fetchEmails(string $mailbox, int $limit = 50): array
    {
        try {
            $messages = $this->graph->createRequest(
                "GET", 
                "/users/{$mailbox}/messages?\$top={$limit}&\$orderby=receivedDateTime desc"
            )->setReturnType(Model\Message::class)->execute();

            $newEmails = [];
            foreach ($messages as $message) {
                $existing = AssignedEmail::where('message_id', $message->getId())->first();
                
                if (!$existing) {
                    $newEmails[] = AssignedEmail::create([
                        'message_id' => $message->getId(),
                        'subject' => $message->getSubject(),
                        'body_preview' => $message->getBodyPreview(),
                        'from_email' => $message->getFrom()->getEmailAddress()->getAddress(),
                        'from_name' => $message->getFrom()->getEmailAddress()->getName(),
                        'received_at' => Carbon::parse($message->getReceivedDateTime()),
                        'status' => 'unassigned'
                    ]);
                }
            }

            return $newEmails;
        } catch (\Exception $e) {
            \Log::error("Graph API Error: " . $e->getMessage());
            return [];
        }
    }

    public function sendReply(string $messageId, string $content): bool
    {
        try {
            $reply = [
                "message" => [
                    "body" => [
                        "contentType" => "HTML",
                        "content" => $content
                    ]
                ]
            ];

            $this->graph->createRequest("POST", "/me/messages/{$messageId}/reply")
                ->attachBody($reply)
                ->execute();

            return true;
        } catch (\Exception $e) {
            \Log::error("Reply failed: " . $e->getMessage());
            return false;
        }
    }
}