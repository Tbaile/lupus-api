<?php

use App\Enums\RoomRoleEnum;
use App\Models\Game;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

uses(RefreshDatabase::class)->group('game');

it('can see games created by room', function () {
    /** @var Game $game */
    $game = Game::factory()->create();
    /** @var Room $room */
    $room = $game->room()->first();
    Game::factory([
        'room_id' => $room->id
    ])->create();
    actingAs($room->owner())
        ->getJson('/api/room/'.$room->id.'/game')
        ->assertOk()
        ->assertJson(
            fn(AssertableJson $json) => $json->has('data', 2,
                fn(AssertableJson $json) => $json->where('id', $game->id)->has('created_at')->has('updated_at')
            )->etc()
        );
});

it('can create a game', function () {
    /** @var Room $room */
    $room = Room::factory()->create();
    $users = User::factory()->count(3)->create();
    foreach ($users as $user) {
        $room->users()->attach($user->id, ['role' => RoomRoleEnum::PLAYER()]);
    }
    $randomUser = User::factory()->create();
    $url = '/api/room/'.$room->id.'/game';
    actingAs($room->owner())->postJson($url, [
        'users' => $users->pluck('id')->toArray()
    ])->assertCreated();
    assertDatabaseCount('games', 1);
    actingAs($room->owner())->postJson($url, [
        'users' => [
            $randomUser->id
        ]
    ])->assertJsonValidationErrors('users.0');
    actingAs($room->owner())->postJson($url)->assertJsonValidationErrors('users');
});
