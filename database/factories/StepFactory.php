<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'recipe_id'   => Recipe::inRandomOrder()->first()?->id ?? Recipe::factory(),
            'step_number' => $this->faker->numberBetween(1,5),
            'description' => $this->faker->sentence(10),
        ];
    }
}
