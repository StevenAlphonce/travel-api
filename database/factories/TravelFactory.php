<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;



/**
 * @extends Factory<Travel>
 */
class TravelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->slug(),
            'is_public' => $this->faker->boolean(),
            'description' => $this->faker->text(100),
            'number_of_days' => $this->faker->numberBetween(1, 14),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Travel $travel): void {
            Tour::factory()
                ->count(5)
                ->create([
                    'travel_id' => $travel->id,
                ]);
        });
    }
}
