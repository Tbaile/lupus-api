<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLogin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:login');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\UserLogin  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UserLogin $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('The provided credentials are incorrect.')],
            ]);
        }
        return response()->json(
            [
                'data' => [
                    'token' => $user->createToken(now()->toISOString())->plainTextToken
                ]
            ]
        );
    }
}
