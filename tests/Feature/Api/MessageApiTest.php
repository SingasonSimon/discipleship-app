<?php

namespace Tests\Feature\Api;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->pastor = User::factory()->create(['role' => User::ROLE_PASTOR]);
    }

    /** @test */
    public function admin_can_get_messages_via_api()
    {
        Message::factory()->count(3)->create();

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/messages');

        $response->assertOk();
    }

    /** @test */
    public function admin_can_create_message_via_api()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $messageData = [
            'message_type' => 'general',
            'channel' => 'email',
            'subject' => 'Test Subject',
            'content' => 'Test message content',
            'recipients' => ['all_members'],
            'schedule_type' => 'immediate',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/messages', $messageData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('messages', [
            'message_type' => 'general',
            'channel' => 'email',
        ]);
    }
}

