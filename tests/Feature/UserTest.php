<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class)->group('user');

beforeEach(fn() => $this->user = User::factory()->create());

it('can register', function () {
    /** @var User $user */
    $user = User::factory()->make();
    postJson('/api/user/register', $user->only(['name', 'email', 'password']))
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json->has('data.token'));
    assertDatabaseHas('users', [
        'name' => $user->name,
        'email' => $user->email
    ]);
    assertDatabaseCount('personal_access_tokens', 1);
    postJson('/api/user/register')->assertInvalid(['name', 'email']);
});

it('can login ', function () {
    postJson('/api/user/login',
        [
            'email' => $this->user->email,
            'password' => 'password'
        ]
    )->assertOk()
        ->assertJson(fn(AssertableJson $json) => $json->has('data.token'));
    assertDatabaseCount('personal_access_tokens', 1);
    postJson('/api/user/login',
        [
            'email' => $this->user->email,
            'password' => 'passwor'
        ]
    )->assertInvalid(['email']);
});

it('can get self user', function () {
    getJson('/api/user/self')
        ->assertUnauthorized();
    actingAs($this->user)->get('/api/user/self')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.id', $this->user->id)
        ->assertJsonPath('data.name', $this->user->name)
        ->assertJsonPath('data.email', $this->user->email);
});
