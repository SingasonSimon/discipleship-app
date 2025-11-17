<?php

namespace Tests\Feature\Api;

use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /** @test */
    public function admin_can_get_sessions_via_api()
    {
        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);
        ClassSession::factory()->count(3)->create(['class_id' => $class->id]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson("/api/v1/classes/{$class->id}/sessions");

        $response->assertOk();
    }

    /** @test */
    public function admin_can_create_session_via_api()
    {
        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $sessionData = [
            'class_id' => $class->id,
            'session_date' => now()->addDays(7)->format('Y-m-d'),
            'topic' => 'Test Session',
            'location' => 'Main Hall',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson("/api/v1/classes/{$class->id}/sessions", $sessionData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('class_sessions', [
            'class_id' => $class->id,
            'topic' => 'Test Session',
        ]);
    }
}

