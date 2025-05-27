<?php

namespace Tests\Feature;

use App\Models\AssignedEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_emails()
    {
        $user = User::factory()->create();
        $email = AssignedEmail::factory()->create(['assigned_to' => $user->id]);

        $response = $this->actingAs($user)->get('/emails');
        $response->assertStatus(200);
        $response->assertViewHas('emails');
        $response->assertSee($email->subject);
    }

    public function test_admin_can_view_all_emails()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $email = AssignedEmail::factory()->create();

        $response = $this->actingAs($admin)->get('/emails');
        $response->assertStatus(200);
        $response->assertSee($email->subject);
    }

    public function test_user_cannot_view_others_emails()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $email = AssignedEmail::factory()->create(['assigned_to' => $user2->id]);

        $response = $this->actingAs($user1)->get('/emails/' . $email->id);
        $response->assertStatus(403);
    }

    public function test_admin_can_assign_email()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $email = AssignedEmail::factory()->create(['status' => 'unassigned']);

        $response = $this->actingAs($admin)
            ->post('/emails/' . $email->id . '/assign', [
                'assigned_to' => $user->id
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('assigned', $email->fresh()->status);
        $this->assertEquals($user->id, $email->fresh()->assigned_to);
    }

    public function test_user_can_update_status()
    {
        $user = User::factory()->create();
        $email = AssignedEmail::factory()->create(['assigned_to' => $user->id]);

        $response = $this->actingAs($user)
            ->post('/emails/' . $email->id . '/status/in_progress');

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('in_progress', $email->fresh()->status);
    }

    public function test_user_can_send_reply()
    {
        $user = User::factory()->create();
        $email = AssignedEmail::factory()->create([
            'assigned_to' => $user->id,
            'status' => 'in_progress'
        ]);

        $response = $this->actingAs($user)
            ->post('/emails/' . $email->id . '/reply', [
                'reply_content' => 'Test reply content'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $email->fresh()->status);
    }
}
