<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'travel_id' => Travel::factory(),
            'name'=>$this->faker->name(),
            'start_date'=>$this->faker->date(),
            'ending_date'=>$this->faker->date(),
            'price' => $this->faker->randomFloat(2,0,1000),
        ];
    }
}
