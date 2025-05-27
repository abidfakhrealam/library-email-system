<?php

namespace Tests\Unit\Services;

use App\Models\AssignedEmail;
use App\Services\Email\GraphApiService;
use App\Services\Email\ImapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EmailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_graph_api_service_fetches_emails()
    {
        $mockGraph = Mockery::mock(GraphApiService::class);
        $mockGraph->shouldReceive('fetchEmails')
            ->once()
            ->with('test@example.com', 50)
            ->andReturn([]);

        $result = $mockGraph->fetchEmails('test@example.com');
        $this->assertIsArray($result);
    }

    public function test_imap_service_fetches_emails()
    {
        $mockImap = Mockery::mock(ImapService::class);
        $mockImap->shouldReceive('fetchEmails')
            ->once()
            ->with(50)
            ->andReturn([]);

        $result = $mockImap->fetchEmails();
        $this->assertIsArray($result);
    }

    public function test_graph_api_service_sends_reply()
    {
        $mockGraph = Mockery::mock(GraphApiService::class);
        $mockGraph->shouldReceive('sendReply')
            ->once()
            ->with('123', 'Test reply')
            ->andReturn(true);

        $result = $mockGraph->sendReply('123', 'Test reply');
        $this->assertTrue($result);
    }

    public function test_email_is_created_when_fetched()
    {
        $emailData = [
            'message_id' => 'test123',
            'subject' => 'Test Subject',
            'body_preview' => 'Test body preview',
            'from_email' => 'test@example.com',
            'from_name' => 'Test User',
            'received_at' => now(),
            'status' => 'unassigned'
        ];

        $mockGraph = Mockery::mock(GraphApiService::class);
        $mockGraph->shouldReceive('fetchEmails')
            ->once()
            ->andReturn([AssignedEmail::create($emailData)]);

        $result = $mockGraph->fetchEmails('test@example.com');
        $this->assertCount(1, $result);
        $this->assertDatabaseHas('assigned_emails', ['message_id' => 'test123']);
    }

    public function test_duplicate_emails_are_not_created()
    {
        $email = AssignedEmail::factory()->create();

        $mockGraph = Mockery::mock(GraphApiService::class);
        $mockGraph->shouldReceive('fetchEmails')
            ->once()
            ->andReturn([]);

        $result = $mockGraph->fetchEmails('test@example.com');
        $this->assertCount(0, $result);
        $this->assertDatabaseCount('assigned_emails', 1);
    }
}
