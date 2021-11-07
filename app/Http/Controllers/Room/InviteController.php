<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomInviteRequest;
use App\Models\Room;

class InviteController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:update,room');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\StoreRoomInviteRequest  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreRoomInviteRequest $request, Room $room)
    {
        foreach ($request->validated()['users'] as $id) {
            $room->players()->attach($id);
        }
    }
}
