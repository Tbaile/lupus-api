<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertForbidden();
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
}
