<?php

namespace Tests\Unit\Services;

use App\Models\Member;
use App\Models\Message;
use App\Models\MessageLog;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MessageService $messageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageService = new MessageService();
    }

    /** @test */
    public function it_sends_email_message_to_member_with_user()
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);
        $member = Member::factory()->create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);

        $message = Message::factory()->create([
            'channel' => 'email',
            'payload' => [
                'subject' => 'Test Subject',
                'recipients' => ['all_members'],
            ],
            'template' => 'Test message content',
        ]);

        $results = $this->messageService->sendMessage($message);

        $this->assertEquals(1, $results['success']);
        $this->assertEquals(0, $results['failed']);
        $this->assertDatabaseHas('message_logs', [
            'message_id' => $message->id,
            'recipient' => 'test@example.com',
            'channel' => 'email',
            'result' => 'success',
        ]);
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'sent',
        ]);
    }

    /** @test */
    public function it_handles_email_message_without_user()
    {
        $member = Member::factory()->create([
            'user_id' => null,
            'email' => 'test@example.com',
        ]);

        $message = Message::factory()->create([
            'channel' => 'email',
            'payload' => [
                'subject' => 'Test Subject',
                'recipients' => ['all_members'],
            ],
            'template' => 'Test message content',
        ]);

        $results = $this->messageService->sendMessage($message);

        $this->assertEquals(0, $results['success']);
        $this->assertEquals(1, $results['failed']);
        $this->assertDatabaseHas('message_logs', [
            'message_id' => $message->id,
            'result' => 'failed',
        ]);
    }

    /** @test */
    public function it_handles_multiple_recipients()
    {
        Notification::fake();

        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $member1 = Member::factory()->create(['user_id' => $user1->id]);
        $member2 = Member::factory()->create(['user_id' => $user2->id]);

        $message = Message::factory()->create([
            'channel' => 'email',
            'payload' => [
                'subject' => 'Test Subject',
                'recipients' => ['all_members'],
            ],
            'template' => 'Test message',
        ]);

        $results = $this->messageService->sendMessage($message);

        $this->assertEquals(2, $results['success']);
        $this->assertEquals(0, $results['failed']);
    }

    /** @test */
    public function it_handles_unknown_channel()
    {
        $member = Member::factory()->create();

        $message = Message::factory()->create([
            'channel' => 'unknown',
            'payload' => [
                'recipients' => ['all_members'],
            ],
            'template' => 'Test',
        ]);

        $results = $this->messageService->sendMessage($message);

        $this->assertEquals(0, $results['success']);
        $this->assertEquals(1, $results['failed']);
    }
}

