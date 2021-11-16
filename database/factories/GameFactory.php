<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    /**
     * @inerhitDoc
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory()
        ];
    }
}
