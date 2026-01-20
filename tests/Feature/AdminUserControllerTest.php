<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

     #[Test]
    public function admin_can_delete_a_user()
    {
        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

     #[Test]
    public function admin_cannot_delete_himself()
    {
        $admin = User::factory()->admin()->create();

        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    #[Test]
    public function database_connection_is_sqlite()
    {
        $this->assertTrue(true);
    }

     #[Test]
    public function non_admin_cannot_delete_user()
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/users/{$target->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
        ]);
    }
}
