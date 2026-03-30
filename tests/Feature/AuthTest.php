<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected const string PASSWORD = 'password123';

    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '12345678901',
            'password' => self::PASSWORD,
        ];

        $response = $this->postJson(route('auth.register'), $userData);
        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'authorisation']]);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make(self::PASSWORD),
        ]);

        $response = $this->postJson(route('auth.login'), [
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

        $response = $this->postJson(route('auth.login'), [
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
            ->getJson(route('auth.me'));

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('auth.logout'));

        $response->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_authenticated_user_can_refresh_token()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(route('auth.refresh'));

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token', 'type']]);
    }
}
