<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_authenticated_user()
    {
        $user = User::factory()->create([
            'full_name' => 'John Doe',
            'address' => '123 Main St',
        ]);
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $user->id,
                'email' => $user->email,
                'full_name' => 'John Doe',
                'address' => '123 Main St',
            ]);
    }

    #[Test]
    public function it_updates_user_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'full_name' => 'Old Full Name',
            'email' => 'oldemail@example.com',
            'address' => 'Old Address',
            'role' => 'student',
        ]);
        $this->actingAs($user, 'sanctum');

        $payload = [
            'name' => 'New Name',
            'full_name' => 'New Full Name',
            'email' => 'newemail@example.com',
            'address' => 'New Address',
            'role' => 'student',
        ];

        $response = $this->putJson('/api/user', $payload);

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'New Name',
                'full_name' => 'New Full Name',
                'email' => 'newemail@example.com',
                'address' => 'New Address',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'full_name' => 'New Full Name',
            'email' => 'newemail@example.com',
            'address' => 'New Address',
        ]);
    }

    #[Test]
    public function it_updates_user_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $this->actingAs($user, 'sanctum');

        $payload = [
            'current_password' => 'old_password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ];

        $response = $this->patchJson('/api/user/password', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);

        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));
    }

    #[Test]
    public function it_deletes_user_account()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user, 'sanctum');

        $payload = [
            'password' => 'password',
        ];

        $response = $this->deleteJson('/api/user', $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Account deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
