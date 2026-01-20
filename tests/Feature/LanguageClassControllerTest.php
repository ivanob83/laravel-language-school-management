<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LanguageClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LanguageClassControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_classes()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        LanguageClass::factory()->count(5)->create();

        $response = $this->getJson('/api/language-classes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'schedule_time', 'status', 'professor', 'students_count']
                ],
                'links',
                'meta',
            ]);
    }

    #[Test]
    public function non_admin_cannot_list_classes()
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/language-classes');

        $response->assertStatus(403);
    }

    #[Test]
    public function it_shows_single_class()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $languageClass = LanguageClass::factory()->create();

        $response = $this->getJson("/api/language-classes/{$languageClass->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $languageClass->id,
                'title' => $languageClass->title,
            ]);
    }

    #[Test]
    public function admin_can_create_class_with_students()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $professor = User::factory()->professor()->create();
        $students = User::factory()->count(2)->student()->create();

        $payload = [
            'title' => 'Spanish 101',
            'description' => 'Basic Spanish',
            'professor_id' => $professor->id,
            'schedule_time' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'student_ids' => $students->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/language-classes', $payload);
        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'Spanish 101',
                'description' => 'Basic Spanish',
                'status' => 'assigned',
            ]);

        $this->assertDatabaseHas('language_classes', ['title' => 'Spanish 101']);

        $createdClass = LanguageClass::where('title', 'Spanish 101')->first();
        $this->assertDatabaseHas('language_class_assignments', [
            'language_class_id' => $createdClass->id,
            'student_id' => $students[0]->id,
            'status' => 'assigned',
        ]);
    }

    #[Test]
    public function admin_can_update_class_and_sync_students()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $languageClass = LanguageClass::factory()->create();
        $newStudents = User::factory()->count(2)->student()->create();

        $payload = [
            'title' => 'Updated Class Title',
            'student_ids' => $newStudents->pluck('id')->toArray(),
        ];

        $response = $this->putJson("/api/language-classes/{$languageClass->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Class Title']);

        foreach ($newStudents as $student) {
            $this->assertDatabaseHas('language_class_assignments', [
                'language_class_id' => $languageClass->id,
                'student_id' => $student->id,
                'status' => 'assigned',
            ]);
        }
    }

    #[Test]
    public function admin_can_delete_class()
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin, 'sanctum');

        $languageClass = LanguageClass::factory()->create();

        $response = $this->deleteJson("/api/language-classes/{$languageClass->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Language class deleted']);

        $this->assertDatabaseMissing('language_classes', ['id' => $languageClass->id]);
    }

    #[Test]
    public function professor_can_confirm_completion_for_their_class()
    {
        //$this->withoutMiddleware(\App\Http\Middleware\RoleMiddleware::class);
        $professor = User::factory()->professor()->create();
        $this->actingAs($professor, 'sanctum');

        $languageClass = LanguageClass::factory()->create(['professor_id' => $professor->id]);

        $response = $this->postJson("/api/language-classes/{$languageClass->id}/complete");

        $response->assertOk()
            ->assertJsonFragment(['status' => 'completed']);

        $this->assertDatabaseHas('language_classes', [
            'id' => $languageClass->id,
            'status' => 'completed',
        ]);
    }

    #[Test]
    public function professor_cannot_confirm_completion_for_other_class()
    {
        $professor = User::factory()->professor()->create();
        $this->actingAs($professor, 'sanctum');

        $otherProfessor = User::factory()->professor()->create();
        $languageClass = LanguageClass::factory()->create(['professor_id' => $otherProfessor->id]);

        $response = $this->postJson("/api/language-classes/{$languageClass->id}/complete");

        $response->assertStatus(403);
    }
}
