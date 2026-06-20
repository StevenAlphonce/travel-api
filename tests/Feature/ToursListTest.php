<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_returns_paginated_data_for_travel_sorted_by_start_date(): void
    {
        $travel = Travel::factory()->create(['slug' => 'foo-bar']);
        $otherTravel = Travel::factory()->create();

        for ($day = 12; $day >= 1; $day--) {
            Tour::factory()->create([
                'travel_id' => $travel->id,
                'name' => 'Tour '.$day,
                'start_date' => '2026-01-'.str_pad((string) $day, 2, '0', STR_PAD_LEFT),
                'price' => 100,
            ]);
        }

        Tour::factory()->count(3)->create(['travel_id' => $otherTravel->id]);

        $response = $this->getJson('/api/v1/travels/foo-bar/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', 12);
        $response->assertJsonPath('data.0.name', 'Tour 1');
        $response->assertJsonPath('data.0.start_date', '2026-01-01');
    }

    public function test_tours_list_can_be_filtered_by_price_and_start_date_range(): void
    {
        $travel = Travel::factory()->create(['slug' => 'foo-bar']);
        $otherTravel = Travel::factory()->create();

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Matching tour',
            'start_date' => '2026-02-15',
            'price' => 150,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Too cheap',
            'start_date' => '2026-02-15',
            'price' => 99,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Too expensive',
            'start_date' => '2026-02-15',
            'price' => 201,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Too early',
            'start_date' => '2026-01-31',
            'price' => 150,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Too late',
            'start_date' => '2026-03-01',
            'price' => 150,
        ]);

        Tour::factory()->create([
            'travel_id' => $otherTravel->id,
            'name' => 'Other travel matching tour',
            'start_date' => '2026-02-15',
            'price' => 150,
        ]);

        $response = $this->getJson('/api/v1/travels/foo-bar/tours?priceFrom=100&priceTo=200&dateFrom=2026-02-01&dateTo=2026-02-28');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.name', 'Matching tour');
    }

    public function test_tours_list_can_be_sorted_by_price_ascending_then_start_date(): void
    {
        $travel = Travel::factory()->create(['slug' => 'foo-bar']);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'High price',
            'start_date' => '2026-03-10',
            'price' => 300,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Low price later',
            'start_date' => '2026-03-20',
            'price' => 100,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Low price earlier',
            'start_date' => '2026-03-01',
            'price' => 100,
        ]);

        $response = $this->getJson('/api/v1/travels/foo-bar/tours?sortBy=price&sortDirection=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'Low price earlier');
        $response->assertJsonPath('data.1.name', 'Low price later');
        $response->assertJsonPath('data.2.name', 'High price');
    }

    public function test_tours_list_can_be_sorted_by_price_descending_then_start_date(): void
    {
        $travel = Travel::factory()->create(['slug' => 'foo-bar']);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Low price',
            'start_date' => '2026-04-10',
            'price' => 100,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'High price later',
            'start_date' => '2026-04-20',
            'price' => 300,
        ]);

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'High price earlier',
            'start_date' => '2026-04-01',
            'price' => 300,
        ]);

        $response = $this->getJson('/api/v1/travels/foo-bar/tours?sortBy=price&sortDirection=desc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', 'High price earlier');
        $response->assertJsonPath('data.1.name', 'High price later');
        $response->assertJsonPath('data.2.name', 'Low price');
    }
}
