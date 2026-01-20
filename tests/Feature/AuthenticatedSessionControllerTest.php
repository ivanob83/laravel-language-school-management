<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
    

class AuthenticatedSessionControllerTest extends TestCase
{
    use RefreshDatabase;

     #[Test]
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
            ]);
    }

     #[Test]
    public function user_cannot_login_with_wrong_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

     #[Test]
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/logout');

        $response->assertNoContent(); // 204

        $this->assertCount(0, $user->tokens()->get());
    }

     #[Test]
    public function unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }
}
