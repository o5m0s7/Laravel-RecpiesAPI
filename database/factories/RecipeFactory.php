<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'image_path'       => 'default.jpg',
            'cooking_time' => $this->faker->numberBetween(10, 120),
            'prep_time' => $this->faker->numberBetween(5, 60),
            'category_name' => Category::inRandomOrder()->first()?->name ?? Category::factory(),
        ];
    }
}
