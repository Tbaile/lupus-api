<?php

namespace App\Models;

use App\Enums\RoomRoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $attributes = [
        'private' => 0
    ];

    protected $fillable = [
        'name',
        'private',
        'password'
    ];

    protected $casts = [
        'private' => 'bool'
    ];

    /**
     * Retrieve every user that is a participant of the room.
     * This also fetches the role of the user as pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot(['role']);
    }

    /**
     * Retrieve the owner of the game.
     *
     * @return \App\Models\User
     */
    public function owner(): User
    {
        return $this->users()->wherePivot('role', RoomRoleEnum::OWNER())->first();
    }

    /**
     * Retrieve the game that are created by this room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
