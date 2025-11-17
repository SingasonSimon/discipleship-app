<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorshipTest extends TestCase
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
    public function admin_can_view_mentorships_index()
    {
        $this->actingAs($this->admin);

        Mentorship::factory()->count(3)->create();

        $response = $this->get('/mentorships');

        $response->assertOk();
        $response->assertViewIs('mentorships.index');
    }

    /** @test */
    public function admin_can_create_mentorship()
    {
        $this->actingAs($this->admin);

        $member = Member::factory()->create();
        $mentor = User::factory()->create(['role' => User::ROLE_PASTOR]);

        $mentorshipData = [
            'member_id' => $member->id,
            'mentor_id' => $mentor->id,
            'start_date' => now()->format('Y-m-d'),
            'status' => 'active',
            'meeting_frequency' => 'weekly',
        ];

        $response = $this->post('/mentorships', $mentorshipData);

        $response->assertRedirect();
        $this->assertDatabaseHas('mentorships', [
            'member_id' => $member->id,
            'mentor_id' => $mentor->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_view_mentorship_statistics()
    {
        $this->actingAs($this->admin);

        Mentorship::factory()->count(5)->create(['status' => 'active']);

        $response = $this->get('/mentorships/statistics');

        $response->assertOk();
        $response->assertViewIs('mentorships.statistics');
    }

    /** @test */
    public function admin_can_export_mentorships_to_csv()
    {
        $this->actingAs($this->admin);

        Mentorship::factory()->count(3)->create();

        $response = $this->get('/mentorships/export');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv');
        $response->assertHeader('Content-Disposition', 'attachment; filename="mentorships_export_' . date('Y-m-d') . '.csv"');
    }

    /** @test */
    public function member_cannot_access_mentorships()
    {
        $this->actingAs($this->member);

        $response = $this->get('/mentorships');

        $response->assertStatus(403);
    }
}

