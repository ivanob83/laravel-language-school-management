<?php

namespace Tests\Feature;

use App\Models\LanguageClass;
use App\Models\LanguageClassAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LanguageClassAssignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_all_assignments()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $assignments = LanguageClassAssignment::factory()->count(3)->create();

        $response = $this->getJson('/api/language-class-assignments');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'language_class_id', 'student_id', 'status', 'created_at', 'updated_at']
                ]
            ]);
    }

    #[Test]
    public function admin_can_view_single_assignment()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $assignment = LanguageClassAssignment::factory()->create();

        $response = $this->getJson("/api/language-class-assignments/{$assignment->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $assignment->id,
                'status' => $assignment->status,
            ]);
    }

    #[Test]
    public function admin_can_create_assignment()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $languageClass = LanguageClass::factory()->create();
        $student = User::factory()->student()->create();

        $payload = [
            'language_class_id' => $languageClass->id,
            'student_id' => $student->id,
            'status' => 'assigned',
        ];

        $response = $this->postJson('/api/language-class-assignments', $payload);

        $response->assertCreated()
            ->assertJsonFragment([
                'language_class_id' => $languageClass->id,
                'student_id' => $student->id,
                'status' => 'assigned',
            ]);

        $this->assertDatabaseHas('language_class_assignments', $payload);
    }

    #[Test]
    public function admin_can_update_assignment_status()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $assignment = LanguageClassAssignment::factory()->create([
            'status' => 'assigned',
        ]);

        $payload = [
            'status' => 'passed',
        ];

        $response = $this->putJson("/api/language-class-assignments/{$assignment->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['status' => 'passed']);

        $this->assertDatabaseHas('language_class_assignments', [
            'id' => $assignment->id,
            'status' => 'passed',
        ]);
    }

    #[Test]
    public function admin_can_delete_assignment()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $assignment = LanguageClassAssignment::factory()->create();

        $response = $this->deleteJson("/api/language-class-assignments/{$assignment->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'Language class assignment deleted',
            ]);

        $this->assertDatabaseMissing('language_class_assignments', [
            'id' => $assignment->id,
        ]);
    }

    #[Test]
    public function student_cannot_create_assignment()
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student, 'sanctum');

        $assignment = LanguageClassAssignment::factory()->make();

        $response = $this->postJson('/api/language-class-assignments', $assignment->toArray());

        $response->assertForbidden(); // 403 due to role middleware
    }
}
