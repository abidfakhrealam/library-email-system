<?php

namespace App\Services\Email;

use jamesiarmes\PhpEws\Client;
use jamesiarmes\PhpEws\Request\FindItemType;
use jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseFolderIdsType;
use jamesiarmes\PhpEws\Enumeration\DefaultShapeNamesType;
use jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use jamesiarmes\PhpEws\Type\ItemResponseShapeType;
use App\Models\AssignedEmail;
use Carbon\Carbon;

class EwsService
{
    private $client;

    public function __construct(string $username, string $password, string $server)
    {
        $this->client = new Client($server, $username, $password);
        $this->client->setVersion(Client::VERSION_2016);
    }

    public function fetchEmails(string $mailbox, int $limit = 50): array
    {
        $request = new FindItemType();
        $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
        
        $folder = new DistinguishedFolderIdType();
        $folder->Id = DistinguishedFolderIdNameType::INBOX;
        $request->ParentFolderIds->DistinguishedFolderId[] = $folder;
        
        $request->ItemShape = new ItemResponseShapeType();
        $request->ItemShape->BaseShape = DefaultShapeNamesType::ALL_PROPERTIES;
        
        $response = $this->client->FindItem($request);
        $newEmails = [];
        
        foreach ($response->ResponseMessages->FindItemResponseMessage as $message) {
            if ($message->ResponseClass != ResponseClassType::SUCCESS) {
                continue;
            }
            
            foreach (array_slice($message->RootFolder->Items->Message, 0, $limit) as $item) {
                $existing = AssignedEmail::where('message_id', $item->ItemId->Id)->first();
                
                if (!$existing) {
                    $newEmails[] = AssignedEmail::create([
                        'message_id' => $item->ItemId->Id,
                        'subject' => $item->Subject,
                        'body_preview' => mb_substr(strip_tags($item->Body->_), 0, 200),
                        'from_email' => $item->From->Mailbox->EmailAddress,
                        'from_name' => $item->From->Mailbox->Name,
                        'received_at' => Carbon::parse($item->DateTimeReceived),
                        'status' => 'unassigned'
                    ]);
                }
            }
        }
        
        return $newEmails;
    }

    public function sendReply(string $messageId, string $content): bool
    {
        try {
            $reply = $this->client->CreateItem([
                'MessageDisposition' => 'SendAndSaveCopy',
                'Items' => [
                    'Message' => [
                        'ItemId' => ['Id' => $messageId],
                        'Body' => [
                            'BodyType' => 'HTML',
                            '_' => $content
                        ]
                    ]
                ]
            ]);

            return $reply->ResponseMessages->CreateItemResponseMessage[0]->ResponseClass 
                === ResponseClassType::SUCCESS;
        } catch (\Exception $e) {
            \Log::error("EWS reply failed: " . $e->getMessage());
            return false;
        }
    }
}
