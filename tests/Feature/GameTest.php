<?php

namespace Tests\Feature;

use App\Enums\RoomRoleEnum;
use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Can we see every game assigned to a room?
     */
    public function test_index_games()
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
                    return $json->has('data', 2, function (AssertableJson $json) use ($game) {
                        return $json->where('id', $game->id)->has('created_at')->has('updated_at');
                    })->etc();
                }
            );
    }

    /**
     * Assert tha game is successfully created.
     */
    public function test_store_game()
    {
        /** @var Room $room */
        $room = Room::factory()->create();
        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            $room->users()->attach($user->id, ['role' => RoomRoleEnum::PLAYER()]);
        }
        $randomUser = User::factory()->create();
        $url = '/api/room/'.$room->id.'/game';
        $response = $this->actingAs($room->owner())->postJson($url, [
            'users' => $users->pluck('id')->toArray()
        ]);
        $response->assertCreated();
        $this->assertDatabaseCount('games', 1);
        $response = $this->actingAs($room->owner())->postJson($url, [
            'users' => [
                $randomUser->id
            ]
        ]);
        $response->assertJsonValidationErrors('users.0');
        $response = $this->actingAs($room->owner())->postJson($url);
        $response->assertJsonValidationErrors('users');
    }
}
