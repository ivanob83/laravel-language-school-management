<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function updateProfile(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {
        if (! Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function deleteAccount(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        $user->delete();
    }
}
