<?php

namespace App\Http\Controllers;

use App\Enums\RoomRoleEnum;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->authorizeResource(Room::class);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRoomRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = new Room($request->validated());
        $room->save();
        $room->users()->attach($request->user(), ['role' => RoomRoleEnum::OWNER()]);
        return (new RoomResource($room))->response();
    }
}
