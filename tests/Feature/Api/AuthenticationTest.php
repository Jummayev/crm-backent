<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_username_and_password(): void
    {
        $response = $this->postJson('/api/v1/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password']);
    }

    public function test_authenticated_user_can_get_their_info(): void
    {
        $user = User::factory()->create(['username' => 'testuser']);

        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $user->id,
                    'username' => 'testuser',
                ],
            ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create(['username' => 'testuser']);

        Passport::actingAs($user);

        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logout successful',
            ]);
    }

    public function test_api_returns_error_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthenticated',
            ]);
    }

    public function test_api_cors_headers_are_set(): void
    {
        $response = $this->options('/api/v1/login');

        $response->assertStatus(200)
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }
}
