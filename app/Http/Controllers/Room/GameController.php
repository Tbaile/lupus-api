<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGameRequest;
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

    /**
     * Create a new game with the given users.
     *
     * @param  \App\Http\Requests\StoreGameRequest  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreGameRequest $request, Room $room): JsonResponse
    {
        $this->authorize('store', [Game::class, $room]);
        $game = new Game();
        $game->room()->associate($room);
        $game->save();
        $game->users()->attach($request->validated()['users']);
        return (new GameResource($game))->response();
    }
}
