<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelsListTest extends TestCase
{

    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_public_travels_list_returns_paginated_data_correctly(): void
    {
        Travel::factory()->count(20)->create(['is_public'=>true]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

         $response->assertJsonCount(10, 'data');
         $response->assertJsonPath('meta.last_page', 2);
         $response->assertJsonPath('meta.total', 20);

    }

    public function test_travels_list_show_only_public_records(): void
    {
        $public_travels=Travel::factory()->create(['is_public'=>true]);
        Travel::factory()->create(['is_public'=>false]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name',$public_travels->name);

    }
}
