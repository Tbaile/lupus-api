<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistration;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\UserRegistration  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UserRegistration $request): JsonResponse
    {
        $user = new User($request->only('name', 'email'));
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return response()->json(
            [
                'token' => $user->createToken(now()->toISOString())->plainTextToken
            ]
        );
    }
}
