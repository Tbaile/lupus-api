<?php

namespace Database\Factories;

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{

    /**
     * Configure the instance of the Room, when created it has to have an owner.
     *
     * @return \Database\Factories\RoomFactory|void
     */
    public function configure()
    {
        return $this->afterCreating(function (Room $room) {
            $room->users()->attach(User::factory()->create(), ['role' => RoomRoleEnum::OWNER()]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->realTextBetween()
        ];
    }

    /**
     * Add a password to the room.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withPassword(): Factory
    {
        return $this->state(function () {
            return [
                'password' => 'password'
            ];
        });
    }

    /**
     * Protect the Room, can be accessed only by invite or password.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function private(): Factory
    {
        return $this->state(function () {
            return [
                'private' => 1,
            ];
        });
    }
}

