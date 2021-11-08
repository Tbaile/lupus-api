<?php

namespace Tests\Feature;

use App\Enums\RoomRoleEnum;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Spatie\Enum\Faker\FakerEnumProvider;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test room creation.
     */
    public function test_create_room()
    {
        $room = Room::factory()->private()->withPassword()->make();
        $user = User::factory()->create();
        $response = $this->postJson('/api/room', [
            'name' => $room->name
        ]);
        $response->assertUnauthorized();
        $response = $this->actingAs($user)
            ->postJson('/api/room', [
                'name' => $room->name
            ]);
        $response->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                return $json->has('data');
            }, function (AssertableJson $json) use ($user, $room) {
                $json->where('name', $room->name)
                    ->where('private', 0)
                    ->where('password', null);
            });
        $response = $this->actingAs($user)
            ->postJson('/api/room', [
                'name' => $room->name,
                'private' => 'damnson'
            ]);
        $response->assertInvalid(['private']);
        $response = $this->actingAs($user)
            ->postJson('/api/room');
        $response->assertInvalid(['name']);
        $response = $this->actingAs($user)
            ->postJson('/api/room', [
                'name' => $room->name,
                'private' => 1,
                'password' => $room->password
            ]);
        $response->assertCreated()
            ->assertJson(function (AssertableJson $json) use ($room, $user) {
                return $json->has('data', function (AssertableJson $json) use ($user, $room) {
                    $json->has('id')
                        ->where('name', $room->name)
                        ->where('private', $room->private)
                        ->where('password', $room->password)
                        ->has('owner.id')
                        ->where('owner.name', $user->name)
                        ->where('owner.email', $user->email);
                });
            });
    }

    /**
     * Check if inviting users actually works.
     */
    public function test_invite_users_to_room()
    {
        $this->faker->addProvider(new FakerEnumProvider($this->faker));
        $room = Room::factory()->create();
        $this->postJson('/api/room/'.$room->id.'/invite')
            ->assertUnauthorized();
        $this->actingAs($room->owner()->first())
            ->postJson('/api/room/'.$room->id.'/invite')
            ->assertInvalid(['users']);
        $users = User::factory()->count(5)->create();
        $postData = $users->map(function (User $user) {
            return [
                'id' => $user->id,
                'role' => $this->faker->randomEnumValue(RoomRoleEnum::class)
            ];
        });
        $response = $this->actingAs($room->owner()->first())
            ->postJson('/api/room/'.$room->id.'/invite', [
                'users' => $postData
            ]);
        $response->assertOk()
            ->assertJson(function (AssertableJson $json) use ($users) {
                $json->has('data', 5, function (AssertableJson $json) use ($users) {
                    $target = $users->first();
                    $json->where('id', $target->id)
                        ->where('name', $target->name)
                        ->where('email', $target->email);
                });
            });
    }
}
