<?php

namespace App\Policies;

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @return bool
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Check if a user is able to invite into a room.
     */
    public function invite(User $user, Room $room): bool
    {
        return $this->update($user, $room);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Room $room): bool
    {
        return $room->users()->wherePivotIn('role', [RoomRoleEnum::OWNER(), RoomRoleEnum::MANAGER()])
            ->where('user_id', $user->id)->exists();
    }
}
