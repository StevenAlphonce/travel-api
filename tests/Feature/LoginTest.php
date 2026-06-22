<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_access_token(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ]);

        $response = $this
            ->withHeader('User-Agent', 'Feature Test Agent')
            ->postJson('/api/v1/login', [
                'email' => 'admin@example.com',
                'password' => 'correct-password',
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['access_token']);

        $this->assertIsString($response->json('access_token'));
        $this->assertStringContainsString('|', $response->json('access_token'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'Feature Test Agent',
        ]);
    }

    public function test_login_rejects_invalid_credentials_without_creating_token(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'correct-password',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'The provided credentials are incorrect.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
