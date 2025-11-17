<?php

namespace Tests\Feature\Api;

use App\Models\DiscipleshipClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->pastor = User::factory()->create(['role' => User::ROLE_PASTOR]);
    }

    /** @test */
    public function admin_can_get_classes_via_api()
    {
        DiscipleshipClass::factory()->count(3)->create();

        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/classes');

        $response->assertOk();
    }

    /** @test */
    public function admin_can_create_class_via_api()
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $classData = [
            'title' => 'New Class',
            'description' => 'Test description',
            'mentor_id' => $this->pastor->id,
            'capacity' => 25,
            'duration_weeks' => 8,
            'schedule_type' => 'weekly',
            'start_date' => now()->addDays(7)->format('Y-m-d'),
            'end_date' => now()->addDays(7)->addWeeks(12)->format('Y-m-d'),
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/classes', $classData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('classes', [
            'title' => 'New Class',
            'mentor_id' => $this->pastor->id,
        ]);
    }
}

