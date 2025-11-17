<?php

namespace Tests\Feature\Api;

use App\Models\Member;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentorshipApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->pastor = User::factory()->create(['role' => User::ROLE_PASTOR]);
    }

    /** @test */
    public function admin_can_get_mentorships_via_api()
    {
        Mentorship::factory()->count(3)->create();

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/mentorships');

        $response->assertOk();
    }

    /** @test */
    public function admin_can_create_mentorship_via_api()
    {
        $member = Member::factory()->create();
        $mentor = User::factory()->create(['role' => User::ROLE_PASTOR]);

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $mentorshipData = [
            'member_id' => $member->id,
            'mentor_id' => $mentor->id,
            'start_date' => now()->format('Y-m-d'),
            'status' => 'active',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/mentorships', $mentorshipData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('mentorships', [
            'member_id' => $member->id,
            'mentor_id' => $mentor->id,
        ]);
    }
}

