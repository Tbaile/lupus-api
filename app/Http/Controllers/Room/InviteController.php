<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomInviteRequest;
use App\Http\Resources\UserResource;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(StoreRoomInviteRequest $request, Room $room): JsonResponse
    {
        $invites = $request->collect('users');
        foreach ($invites as $invite) {
            $room->users()->attach($invite['id'], ['role' => $invite['role']]);
        }
        return UserResource::collection($room->users()
            ->withPivot('role')->whereIn('user_id', $invites->pluck('id'))
            ->get())->response();
    }
}
