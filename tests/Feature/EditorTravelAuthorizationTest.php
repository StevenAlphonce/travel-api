<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EditorTravelAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_travel_update_requires_authentication(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->putJson("/api/v1/admin/travels/{$travel->id}", $this->validPayload());

        $response->assertUnauthorized();
    }

    public function test_travel_update_requires_editor_role(): void
    {
        $travel = Travel::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson("/api/v1/admin/travels/{$travel->id}", $this->validPayload());

        $response->assertForbidden();
    }

    public function test_editor_can_update_travel(): void
    {
        $travel = Travel::factory()->create([
            'name' => 'Original Travel',
            'description' => 'Original description',
            'number_of_days' => 3,
            'is_public' => false,
        ]);
        $user = User::factory()->create();
        $role = Role::create(['name' => 'editor']);

        $user->roles()->attach($role->id);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/admin/travels/{$travel->id}", $this->validPayload());

        $response->assertOk();
        $response->assertJsonPath('data.name', 'Updated Travel');
        $response->assertJsonPath('data.description', 'Updated travel description.');
        $response->assertJsonPath('data.number_of_days', 7);

        $this->assertDatabaseHas('travels', [
            'id' => $travel->id,
            'name' => 'Updated Travel',
            'description' => 'Updated travel description.',
            'number_of_days' => 7,
            'is_public' => true,
        ]);
    }

    private function validPayload(): array
    {
        return [
            'is_public' => true,
            'name' => 'Updated Travel',
            'description' => 'Updated travel description.',
            'number_of_days' => 7,
        ];
    }
}
