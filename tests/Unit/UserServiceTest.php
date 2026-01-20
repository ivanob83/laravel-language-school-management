<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

     #[Test]
    public function it_can_update_user_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $data = [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ];

        $updatedUser = $this->service->updateProfile($user, $data);

        $this->assertEquals('New Name', $updatedUser->name);
        $this->assertEquals('new@example.com', $updatedUser->email);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

     #[Test]
    public function it_can_update_user_password_with_correct_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);

        $this->service->updatePassword($user, 'old_password', 'new_password');

        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));
    }

     #[Test]
    public function it_throws_exception_when_updating_password_with_wrong_current_password()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        $this->service->updatePassword($user, 'wrong_password', 'new_password');
    }

     #[Test]
    public function it_can_delete_user_account_with_correct_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->service->deleteAccount($user, 'password');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

     #[Test]
    public function it_throws_exception_when_deleting_user_with_wrong_password()
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create([
            'password' => Hash::make('correct_password'),
        ]);

        $this->service->deleteAccount($user, 'wrong_password');
    }
}
