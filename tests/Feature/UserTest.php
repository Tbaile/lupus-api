<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
