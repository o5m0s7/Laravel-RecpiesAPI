<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'recipe_id' => Recipe::inRandomOrder()->first()?->id ?? Recipe::factory(),
            'name'      => $this->faker->word(),
            'quantity'  => $this->faker->randomFloat(1, 1, 10),
            'unit'      => $this->faker->randomElement(['g', 'kg', 'ml', 'l', 'piece']),
        ];
    }
}
