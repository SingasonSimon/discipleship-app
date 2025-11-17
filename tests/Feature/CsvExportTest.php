<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DiscipleshipClass;
use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    /** @test */
    public function admin_can_export_members_to_csv()
    {
        $this->actingAs($this->admin);

        Member::factory()->count(3)->create();

        $response = $this->get('/members/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv');
        $response->assertHeader('Content-Disposition', 'attachment; filename="members_export_' . date('Y-m-d') . '.csv"');
    }

    /** @test */
    public function admin_can_export_mentorships_to_csv()
    {
        $this->actingAs($this->admin);

        Mentorship::factory()->count(3)->create();

        $response = $this->get('/mentorships/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv');
    }

    /** @test */
    public function admin_can_export_attendance_to_csv()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);
        $session = ClassSession::factory()->create(['class_id' => $class->id]);
        Attendance::factory()->count(3)->create(['class_session_id' => $session->id]);

        $response = $this->get("/attendance/session/{$session->id}/export");

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv');
    }
}

