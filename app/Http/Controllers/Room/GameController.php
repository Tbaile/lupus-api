<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Room $room): JsonResponse
    {
        $this->authorize('viewAny', [Game::class, $room]);
        return GameResource::collection($room->games()->paginate())->response();
    }
}
