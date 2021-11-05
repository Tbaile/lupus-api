<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration.
     *
     * @return void
     */
    public function test_register()
    {
        $user = User::factory()->make();
        $response = $this->postJson('/api/user/register', $user->only(['name', 'email', 'password']));
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $response = $this->postJson('/api/user/register');
        $response->assertInvalid(['name', 'email']);
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function test_login()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/user/login',
            [
                'email' => $user->email,
                'password' => 'password'
            ]
        );
        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $response = $this->postJson('/api/user/login',
            [
                'email' => $user->email,
                'password' => 'passwor'
            ]
        );
        $response->assertInvalid(['email']);
    }

    /**
     * Checks user self route.
     */
    public function test_self()
    {
        $user = User::factory()->create();
        $this->getJson('/api/user/self')->assertUnauthorized();
        $response = $this->actingAs($user)->get('/api/user/self');
        $response->assertJson(function (AssertableJson $json) {
            return $json->has('data');
        },
        function (AssertableJson $json) use ($user) {
            return $json->where('id', $user->id)
                ->where('name', $user->name)
                ->where('email', $user->email);
        });
    }
}
