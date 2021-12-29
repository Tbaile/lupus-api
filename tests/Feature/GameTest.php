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
    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var Room $room */
    $room = Room::factory()
        ->hasAttached($owner, ['role' => RoomRoleEnum::OWNER()])
        ->create();
    $games = Game::factory()
        ->for($room)
        ->count(5)
        ->create();
    actingAs($owner)
        ->getJson('/api/room/'.$room->id.'/game')
        ->assertOk()
        ->assertJson(
            fn(AssertableJson $json) => $json->has('data', 5,
                fn(AssertableJson $json) => $json->where('id', $games->first()->id)
                    ->where('created_at', $games->first()->created_at->toISOString())
                    ->where('updated_at', $games->first()->updated_at->toISOString()))
            ->etc()
        );
});

it('can create a game', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $players = User::factory()->count(3)->create();
    /** @var Room $room */
    $room = Room::factory()
        ->hasAttached($owner, ['role' => RoomRoleEnum::OWNER()])
        ->hasAttached($players, ['role' => RoomRoleEnum::PLAYER()])
        ->create();
    $url = '/api/room/'.$room->id.'/game';
    actingAs($owner)->postJson($url, [
        'users' => [
            User::factory()->create()->id
        ]
    ])->assertJsonValidationErrors('users.0');
    actingAs($owner)->postJson($url)
        ->assertJsonValidationErrors('users');
    actingAs($owner)->postJson($url, [
        'users' => $players->pluck('id')->toArray()
    ])->assertCreated();
    assertDatabaseCount('games', 1);
});
