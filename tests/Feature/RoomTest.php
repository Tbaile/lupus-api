<?php

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Faker\faker;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

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
    $room = Room::factory()
        ->hasAttached(User::factory(), ['role' => RoomRoleEnum::OWNER()])
        ->create();
    actingAs($room->owner())
        ->postJson('/api/room/'.$room->id.'/invite', $data)
        ->assertInvalid(['users']);
})->with([
    ['users' => []],
    [[]]
]);

test('invitation user params are invalid', function (mixed $id, mixed $role) {
    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var Room $room */
    $room = Room::factory()
        ->hasAttached($owner, ['role' => RoomRoleEnum::OWNER()])
        ->create();
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
    /** @var User $owner */
    $owner = User::factory()->create();
    /** @var Room $room */
    $room = Room::factory()
        ->hasAttached($owner, ['role' => RoomRoleEnum::OWNER()])
        ->create();
    $users = User::factory()->count($userNumber)->create();
    actingAs($owner)
        ->postJson('/api/room/'.$room->id.'/invite', [
            'users' => $users->map(fn(User $user) => [
                'id' => $user->id,
                'role' => faker()->randomElement(RoomRoleEnum::toValues())
            ])
        ])->assertCreated()
        ->assertJson(
            fn(AssertableJson $json) => $json->count('data', $userNumber)
                ->whereType('data', 'array')
                ->whereContains('data', $users->map(fn(User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]))
        );
})->with([5, 10, 15]);

test('user can\'t invite people already in the room', function ($role) {
    /** @var Room $room */
    $room = Room::factory()
        ->hasAttached(User::factory(), ['role' => RoomRoleEnum::OWNER()])
        ->create();
    actingAs($room->owner())
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

test('auth required for index')
    ->getJson('/api/room')
    ->assertUnauthorized();

test('user can see public rooms only', function () {
    /** @var User $user */
    $user = User::factory()->create();
    Room::factory()
        ->hasAttached($user, ['role' => RoomRoleEnum::OWNER()])
        ->count(5)
        ->create();
    /** @var Room $privateRoom */
    $privateRoom = Room::factory()
        ->hasAttached(User::factory(), ['role' => RoomRoleEnum::OWNER()])
        ->private()
        ->create();
    actingAs($user)
        ->getJson('/api/room')
        ->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonMissing(
            [
                'id' => $privateRoom->id,
                'name' => $privateRoom->name,
                'private' => $privateRoom->private,
                'owner' => [
                    'id' => $privateRoom->owner()->id,
                    'name' => $privateRoom->owner()->name,
                    'email' => $privateRoom->owner()->email
                ]
            ],
            true
        );
});

test('user can see owned rooms', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $rooms = Room::factory()
        ->hasAttached($owner, ['role' => RoomRoleEnum::OWNER()])
        ->count(3)
        ->state(new Sequence(
            ['private' => 1],
            ['private' => 0],
        ))->state(new Sequence(
            [
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay()
            ],
            [
                'created_at' => now()->addDay(),
                'updated_at' => now()->addDay()
            ]
        ))
        ->create()
        ->load('users');
    /** @var Room $testCase */
    $expected = $rooms->sortByDesc('created_at')->map(fn(Room $room) => [
        'id' => $room->id,
        'name' => $room->name,
        'private' => $room->private,
        'password' => null,
        'owner' => [
            'id' => $room->owner()->id,
            'name' => $room->owner()->name,
            'email' => $room->owner()->email
        ]
    ])->values()->toArray();
    actingAs($owner)
        ->getJson('/api/room')
        ->assertOk()
        ->assertJson(
            fn(AssertableJson $json) => $json->has('data',
                fn(AssertableJson $json) => $json->whereAll($expected))->etc()
        );
});
