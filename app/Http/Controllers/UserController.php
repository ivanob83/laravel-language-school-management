<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\DTOs\UserDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display the authenticated user.
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): UserResource
    {   
        $user = $this->userService->updateProfile(
            $request->user(),
            UserDTO::fromArray($request->validated())
        );

        return new UserResource($user);
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword(
            $request->user(),
            $request->current_password,
            $request->password
        );

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Delete the authenticated user's account.
     */
    public function destroy(DeleteUserRequest $request): JsonResponse
    {
        $this->userService->deleteAccount(
            $request->user(),
            $request->password
        );

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }
}
