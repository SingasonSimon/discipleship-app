<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->pastor = User::factory()->create(['role' => User::ROLE_PASTOR]);
        $this->member = User::factory()->create(['role' => User::ROLE_MEMBER]);
    }

    /** @test */
    public function admin_can_view_messages_index()
    {
        $this->actingAs($this->admin);

        Message::factory()->count(3)->create();

        $response = $this->get('/messages');

        $response->assertOk();
        $response->assertViewIs('messages.index');
    }

    /** @test */
    public function admin_can_create_email_message()
    {
        $this->actingAs($this->admin);

        $messageData = [
            'message_type' => 'general',
            'channel' => 'email',
            'subject' => 'Test Subject',
            'content' => 'Test message content',
            'recipients' => ['all_members'],
            'schedule_type' => 'immediate',
        ];

        $response = $this->post('/messages', $messageData);

        $response->assertRedirect(route('messages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'message_type' => 'general',
            'channel' => 'email',
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function admin_can_create_scheduled_message()
    {
        $this->actingAs($this->admin);

        $messageData = [
            'message_type' => 'class_reminder',
            'channel' => 'email',
            'subject' => 'Upcoming Class',
            'content' => 'Reminder about your class',
            'recipients' => ['class_members'],
            'schedule_type' => 'scheduled',
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ];

        $response = $this->post('/messages', $messageData);

        $response->assertRedirect(route('messages.index'));
        $this->assertDatabaseHas('messages', [
            'message_type' => 'class_reminder',
            'status' => 'scheduled',
        ]);
    }

    /** @test */
    public function admin_can_view_message_details()
    {
        $this->actingAs($this->admin);

        $message = Message::factory()->create();

        $response = $this->get("/messages/{$message->id}");

        $response->assertOk();
        $response->assertViewIs('messages.show');
    }

    /** @test */
    public function admin_can_edit_message()
    {
        $this->actingAs($this->admin);

        $message = Message::factory()->create([
            'channel' => 'email',
            'message_type' => 'general',
        ]);

        $response = $this->get("/messages/{$message->id}/edit");

        $response->assertOk();
        $response->assertViewIs('messages.edit');
    }

    /** @test */
    public function admin_can_update_message()
    {
        $this->actingAs($this->admin);

        $message = Message::factory()->create([
            'channel' => 'email',
            'message_type' => 'general',
        ]);

        $updateData = [
            'message_type' => 'custom',
            'channel' => 'email',
            'subject' => 'Updated Subject',
            'content' => 'Updated message content',
            'recipients' => ['all_members'],
            'schedule_type' => 'immediate',
        ];

        $response = $this->put("/messages/{$message->id}", $updateData);

        $response->assertRedirect(route('messages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'channel' => 'email',
            'message_type' => 'custom',
        ]);
    }

    /** @test */
    public function admin_can_delete_message()
    {
        $this->actingAs($this->admin);

        $message = Message::factory()->create();

        $response = $this->delete("/messages/{$message->id}");

        $response->assertRedirect(route('messages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }

    /** @test */
    public function admin_can_send_draft_message_immediately()
    {
        Notification::fake();
        $this->actingAs($this->admin);

        $member = Member::factory()->create();
        $user = User::factory()->create(['email' => $member->email]);
        $member->update(['user_id' => $user->id]);

        $message = Message::factory()->create([
            'status' => 'draft',
            'channel' => 'email',
            'payload' => [
                'subject' => 'Test Subject',
                'recipients' => ['all_members'],
            ],
        ]);

        $response = $this->post("/messages/{$message->id}/send-now");

        $response->assertRedirect();
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function cannot_send_non_draft_message_immediately()
    {
        $this->actingAs($this->admin);

        $message = Message::factory()->create([
            'status' => 'sent',
        ]);

        $response = $this->post("/messages/{$message->id}/send-now");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function message_creation_requires_validation()
    {
        $this->actingAs($this->admin);

        $response = $this->post('/messages', []);

        $response->assertSessionHasErrors(['message_type', 'channel', 'content', 'recipients', 'schedule_type']);
    }

    /** @test */
    public function scheduled_message_requires_scheduled_at()
    {
        $this->actingAs($this->admin);

        $messageData = [
            'message_type' => 'general',
            'channel' => 'email',
            'content' => 'Test',
            'recipients' => ['all_members'],
            'schedule_type' => 'scheduled',
            // Missing scheduled_at
        ];

        $response = $this->post('/messages', $messageData);

        $response->assertSessionHasErrors(['scheduled_at']);
    }

    /** @test */
    public function member_cannot_access_messages()
    {
        $this->actingAs($this->member);

        $response = $this->get('/messages');

        $response->assertStatus(403);
    }
}

