<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Room\GameController;
use App\Http\Controllers\Room\InviteController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user/self', [UserController::class, 'self']);
Route::post('/user/register', RegisterController::class);
Route::post('/user/login', LoginController::class);

Route::post('/room/{room}/invite', InviteController::class);
Route::apiResource('room', RoomController::class)->only(['index', 'store']);
Route::apiResource('room.game', GameController::class)->only(['index', 'store']);

