<?php

namespace Tests\Feature;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Can we see every game assigned to a room?
     */
    public function test_index()
    {
        /** @var Game $game */
        $game = Game::factory()->create();
        $room = $game->room()->first();
        Game::factory([
            'room_id' => $room->id
        ])->create();
        Game::factory()->create();
        $response = $this->actingAs($room->owner())->getJson('/api/room/'.$room->id.'/game');
        $response->assertOk()
            ->assertJson(
                function (AssertableJson $json) use ($game) {
                    return $json->has('data', 2, function (AssertableJson $json) use ($json, $game) {
                        return $json->where('id', $game->id)->has('created_at')->has('updated_at');
                    })->etc();
                }
            );
    }
}
