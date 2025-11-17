<?php

namespace Tests\Feature;

use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTest extends TestCase
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
    public function admin_can_create_session()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);

        $sessionData = [
            'class_id' => $class->id,
            'session_date' => now()->addDays(7)->format('Y-m-d'),
            'topic' => 'Test Session Topic',
            'location' => 'Main Hall',
            'duration_minutes' => 90,
        ];

        $response = $this->post("/classes/{$class->id}/sessions", $sessionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_sessions', [
            'class_id' => $class->id,
            'topic' => 'Test Session Topic',
        ]);
    }

    /** @test */
    public function admin_can_view_session_statistics()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);
        $session = ClassSession::factory()->create(['class_id' => $class->id]);

        $response = $this->get("/sessions/{$session->id}/statistics");

        $response->assertOk();
        $response->assertViewIs('sessions.statistics');
    }

    /** @test */
    public function admin_can_mark_attendance_for_session()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);
        $session = ClassSession::factory()->create(['class_id' => $class->id]);
        $member = Member::factory()->create();

        $response = $this->get("/sessions/{$session->id}/attendance");

        $response->assertOk();
        $response->assertViewIs('sessions.attendance');
    }
}

