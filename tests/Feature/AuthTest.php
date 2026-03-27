<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    const string PASSWORD = 'password123';

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '12345678901',
            'password' => self::PASSWORD,
        ];

        $response = $this->postJson('/api/auth/register', $userData);
        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'authorisation']]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make(self::PASSWORD),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => self::PASSWORD,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'authorisation']]);
    }

    public function test_login_fails_with_wrong_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertUnauthorized()
            ->assertJsonPath('status', 'error');
    }

    public function test_authenticated_user_can_get_me()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/me');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }
}
