<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminTourAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_tour_store_requires_authentication(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->postJson("/api/v1/admin/travels/{$travel->id}/tours", $this->validPayload());

        $response->assertUnauthorized();
    }

    public function test_admin_tour_store_requires_admin_role(): void
    {
        $travel = Travel::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson("/api/v1/admin/travels/{$travel->id}/tours", $this->validPayload());

        $response->assertForbidden();
    }

    public function test_admin_can_store_tour_for_travel(): void
    {
        $travel = Travel::factory()->create();
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin']);

        $user->roles()->attach($role->id);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/admin/travels/{$travel->id}/tours", $this->validPayload());

        $response->assertCreated();
        $response->assertJsonPath('data.name', 'Admin Created Tour');

        $this->assertDatabaseHas('tours', [
            'travel_id' => $travel->id,
            'name' => 'Admin Created Tour',
            'start_date' => '2026-07-01',
            'ending_date' => '2026-07-08',
            'price' => 125000,
        ]);
    }

    private function validPayload(): array
    {
        return [
            'name' => 'Admin Created Tour',
            'start_date' => '2026-07-01',
            'ending_date' => '2026-07-08',
            'price' => 1250,
        ];
    }
}
