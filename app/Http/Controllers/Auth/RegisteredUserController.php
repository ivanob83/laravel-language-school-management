<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserField;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): Response
    {

        $user = User::create([
            UserField::Name->value => $request->name,
            UserField::Email->value => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return response()->noContent();
    }
}
