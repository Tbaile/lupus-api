<?php

namespace App\Models;

use App\Enums\RoomRoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Retrieve the owner of the game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function owner(): BelongsToMany
    {
        return $this->users()->wherePivot('role', RoomRoleEnum::OWNER());
    }
}
