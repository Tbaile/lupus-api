<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Auth;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Return the User that is currently authenticated by the token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function self(): JsonResponse
    {
        return (new UserResource(Auth::user()))->response();
    }
}
