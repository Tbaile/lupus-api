<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class RoomFactory extends Factory
{

    /**
     * @inerhitDoc
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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

