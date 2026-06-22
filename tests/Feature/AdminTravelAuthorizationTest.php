<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTravelAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_travel_store_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/admin/travels', $this->validPayload());

        $response->assertUnauthorized();
    }

    public function test_admin_travel_store_requires_admin_role(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/admin/travels', $this->validPayload());

        $response->assertForbidden();
    }

    public function test_admin_can_store_travel(): void
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);

        $user->roles()->attach($role->id);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/admin/travels', $this->validPayload());

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Admin Created Travel');

        $this->assertDatabaseHas('travels', [
            'name' => 'Admin Created Travel',
            'is_public' => true,
        ]);
    }

    private function validPayload(): array
    {
        return [
            'is_public' => true,
            'name' => 'Admin Created Travel',
            'description' => 'Travel created by an admin user.',
            'number_of_days' => 5,
        ];
    }
}
