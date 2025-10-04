<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_can_register()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson(route('auth.register'), $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'created_at',
                         'updated_at',
                     ],
                 ]);

        $this->assertDatabaseHas('users', ['email' => 'johndoe@example.com']);
    }


    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                         'token',
                     ],
                 ]);
    }


    public function test_authenticated_user_can_get_me()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson(route('me'));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'email' => $user->email,
                     ],
                 ]);
    }


    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('logout'));

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Logged out successfully',
                ]);
    }


    public function test_login_with_invalid_credentials_fails()
    {
        $payload = [
            'email' => 'wrong@example.com',
            'password' => 'invalid',
        ];

        $response = $this->postJson(route('auth.login'), $payload);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid credentials',
                 ]);
    }
}
