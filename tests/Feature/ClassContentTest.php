<?php

namespace Tests\Feature;

use App\Models\ClassContent;
use App\Models\DiscipleshipClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->pastor = User::factory()->create(['role' => User::ROLE_PASTOR]);
    }

    /** @test */
    public function admin_can_create_class_content()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create([
            'mentor_id' => $this->admin->id,
            'duration_weeks' => 8,
        ]);

        $contentData = [
            'class_id' => $class->id,
            'title' => 'Test Content',
            'content_type' => 'lesson',
            'week_number' => 1,
            'content' => 'Test content body',
            'is_published' => false,
        ];

        $response = $this->post("/classes/{$class->id}/content", $contentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_contents', [
            'class_id' => $class->id,
            'title' => 'Test Content',
        ]);
    }

    /** @test */
    public function admin_can_toggle_content_publish_status()
    {
        $this->actingAs($this->admin);

        $class = DiscipleshipClass::factory()->create(['mentor_id' => $this->admin->id]);
        $content = ClassContent::factory()->create([
            'class_id' => $class->id,
            'is_published' => false,
        ]);

        $response = $this->patch("/classes/{$class->id}/content/{$content->id}/toggle-publish");

        $response->assertRedirect();
        $this->assertDatabaseHas('class_contents', [
            'id' => $content->id,
            'is_published' => true,
        ]);
    }
}

