<?php

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Faker\faker;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertTrue;

uses(RefreshDatabase::class)->group('room');

test('cannot create room without authentication')
    ->postJson('/api/room', ['name' => 'Test Room'])
    ->assertUnauthorized();

it('cannot create room without name', function (array $data) {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user)
        ->postJson('/api/room', $data)
        ->assertUnprocessable()
        ->assertInvalid('name');
})->with([
    [['name' => null]],
    [['name' => '']],
    [['name' => PHP_EOL]],
    [[]]
]);

it('cannot create invalid room', function ($private) {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user)
        ->postJson('/api/room', [
            'name' => 'invalid room',
            'private' => $private
        ])->assertUnprocessable()
        ->assertInvalid('private');
})->with([
    'no',
    PHP_EOL,
    'yes',
    'reallyboringtext'
]);

test("user can create", function (bool $private, ?string $password) {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user)
        ->postJson('/api/room', [
            'name' => 'room name',
            'private' => $private,
            'password' => $password
        ])
        ->assertCreated()
        ->assertJson(
            fn(AssertableJson $json) => $json->has('data',
                fn(AssertableJson $json) => $json->whereType('id', 'integer')
                    ->where('name', 'room name')
                    ->where('private', $private)
                    ->where('password', $password)
                    ->has('owner')
                    ->whereContains('owner', ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]))
        );
})->with([
    0,
    1
])->with([
    null,
    'password'
]);

test('unauthorized invitation to room', function () {
    /** @var Room $room */
    $room = Room::factory()->create();
    postJson('/api/room/'.$room->id.'/invite')
        ->assertUnauthorized();
});

test('room not found', function () {
    /** @var User $user */
    $user = User::factory()->create();
    actingAs($user)
        ->postJson('/api/room/123/invite')
        ->assertNotFound();
});

test('users array cannot be empty', function (array $data) {
    /** @var Room $room */
    $room = Room::factory()->create();
    /** @var User $owner */
    $owner = User::factory()->create();
    $room->users()->attach($owner, ['role' => RoomRoleEnum::OWNER()]);
    actingAs($owner)
        ->postJson('/api/room/'.$room->id.'/invite', $data)
        ->assertInvalid(['users']);
})->with([
    ['users' => []],
    [[]]
]);

test('invitation user params are invalid', function (mixed $id, mixed $role) {
    /** @var Room $room */
    $room = Room::factory()->create();
    /** @var User $owner */
    $owner = User::factory()->create();
    $room->users()->attach($owner, ['role' => RoomRoleEnum::OWNER()]);
    actingAs($owner)->postJson('/api/room/'.$room->id.'/invite', [
        'users' => [
            [
                'id' => $id,
                'role' => $role
            ]
        ]
    ])->assertInvalid(['users.0.id', 'users.0.role']);
})->with([
    0,
    2,
    null,
    'hello'
])->with([
    'owne',
    0,
    null
]);

test('invite users', function (int $userNumber) {
    /** @var Room $room */
    $room = Room::factory()->create();
    /** @var User $owner */
    $owner = User::factory()->create();
    $room->users()->attach($owner, ['role' => RoomRoleEnum::OWNER()]);
    $users = User::factory()->count($userNumber)->create();
    $data = $users->map(fn(User $user) => [
        'id' => $user->id,
        'role' => faker()->randomElement(RoomRoleEnum::toValues())
    ]);
    $expected = $users->map(fn(User $user) => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email
    ]);
    actingAs($owner)
        ->postJson('/api/room/'.$room->id.'/invite', [
            'users' => $data
        ])->assertCreated()
        ->assertJson(
            fn(AssertableJson $json) => $json->count('data', $userNumber)
                ->whereType('data', 'array')
                ->whereContains('data', $expected->toArray())
        );
})->with([5, 10, 15]);

test('user can\'t invite people already in the room', function ($role) {
    /** @var Room $room */
    $room = Room::factory()->create();
    /** @var User $owner */
    $owner = User::factory()->create();
    $room->users()->attach($owner, ['role' => RoomRoleEnum::OWNER()]);
    actingAs($owner)
        ->postJson('/api/room/'.$room->id.'/invite', [
            'users' => [
                [
                    'id' => $room->owner()->id,
                    'role' => $role
                ]
            ]
        ])->assertInvalid(['users.0.id']);
})->with([
    RoomRoleEnum::OWNER(),
    RoomRoleEnum::MANAGER(),
    RoomRoleEnum::PLAYER()
]);
