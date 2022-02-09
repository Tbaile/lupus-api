<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Day>
 */
class DayFactory extends Factory
{
    /**
     * @inerhitDoc
     */
    public function definition(): array
    {
        return [
            'game_id' => Game::factory()
        ];
    }
}
