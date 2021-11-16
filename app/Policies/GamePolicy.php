<?php

namespace App\Policies;

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class GamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, Room $room): Response|bool
    {
        return $room->users()
            ->wherePivotIn('role', [RoomRoleEnum::OWNER(), RoomRoleEnum::MANAGER()])
            ->where('user_id', $user->id)
            ->exists();
    }
}
